<?php
declare(strict_types=1);

namespace Novatech\Epp;

use Laminas\Config\Config;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Struzik\EPPClient\EPPClient;
use Struzik\EPPClient\NamespaceCollection;
use Struzik\EPPClient\Request\RequestInterface;
use Struzik\EPPClient\Request\Session\LoginRequest;
use Struzik\EPPClient\Request\Session\LogoutRequest;
use Struzik\EPPClient\Response\ResponseInterface;
use Struzik\EPPClient\SocketConnection\StreamSocketConfig;
use Struzik\EPPClient\SocketConnection\StreamSocketConnection;

class Socket
{
    /**
     * @var EPPClient
     */
    private EPPClient $client;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param string $namespace
     * @param string $username
     * @param string $password
     */
    public function __construct(
        protected string $namespace,
        protected string $username = '',
        protected string $password = ''
    ) {
        $this->instantiateEppClient();
        $this->setEppClientNamespaceCollectionOffset();
        $this->connect();
    }
    /**
     * @return void
     */
    private function connect(): void
    {
        if (! $this->isConnected()) {
            $this->client->connect();
            $request = new LoginRequest($this->client);
            $request->setLogin($this->username)
                ->setPassword($this->password)
                ->setLanguage('en')
                ->setProtocolVersion('1.0');
            $this->client->send($request);
        }
    }

    /**
     * instantiateEppClient
     * @return void
     */
    private function instantiateEppClient(): void
    {
        $this->config = (new Config(require "config/epp.config.php"))->epp;
        $uri = $this->config->server->live->server->{$this->namespace}->address;
        $uri = $uri . ":" .    $this->config->port;
        $logger = new Logger('EPP Client');
        $logger->pushHandler(new StreamHandler($this->logfile(), Logger::DEBUG));
        $connectionConfig = new StreamSocketConfig();
        $connectionConfig->uri = $uri;
        $connectionConfig->timeout = 10;
        $connectionConfig->context = [
            'ssl' => [
                'local_cert' =>  $this->config->server->live->cert_file,
            ]
        ];

        $connection = new StreamSocketConnection($connectionConfig, $logger);
        $this->client = new EPPClient($connection, $logger);
        $this->username = $this->config->server->live->username;
        $this->password = $this->config->server->live->server->{$this->namespace}->password;
    }

    /**
     * setEppClientNamespaceOffset
     * @return void
     */
    private function setEppClientNamespaceCollectionOffset(): void
    {
        $this->client->getNamespaceCollection()->offsetSet(
            NamespaceCollection::NS_NAME_ROOT,
            'urn:ietf:params:xml:ns:epp-1.0'
        );
        $this->client->getNamespaceCollection()->offsetSet(
            NamespaceCollection::NS_NAME_CONTACT,
            'urn:ietf:params:xml:ns:contact-1.0'
        );
        $this->client->getNamespaceCollection()->offsetSet(
            NamespaceCollection::NS_NAME_HOST,
            'urn:ietf:params:xml:ns:host-1.0'
        );
        $this->client->getNamespaceCollection()->offsetSet(
            NamespaceCollection::NS_NAME_DOMAIN,
            'urn:ietf:params:xml:ns:domain-1.0'
        );
    }

    /**
     * Execute the Epp Request
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function execute(RequestInterface $request): ResponseInterface
    {
        return  $this->client->send($request);
    }

    /**
     * @return bool
     */
    private function isConnected(): bool
    {
        return $this->client->getConnection()->isOpened();
    }

    /**
     * @return EPPClient
     */
    public function getClient(): EPPClient
    {
        return $this->client;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        if ($this->isConnected()) {
            $this->logout();
        }
        return true;
    }

    /**
     * @return void
     */
    private function logout()
    {
        $this->client->send(new LogoutRequest($this->client));
        $this->client->disconnect();
    }

    /**
     * @return resource|void
     */
    private function logfile()
    {
        if ($handle = fopen($this->config->log_file, 'a')) {
            return $handle;
        }
    }

}