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
	const APP_NAME = 'EPP Client';
    /**
     * @var EPPClient
     */
    private EPPClient $client;

    /**
     * @var Config
     */
    private Config $config;

	/**
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $uri
	 * @param int $timeout
	 */
    public function __construct(
	    private string          $username = '',
	    private string          $password = '',
	    private readonly string $uri = '',
	    private readonly int    $timeout = 10
    ) {
	    $this->config = (new Config(require "config/epp.config.php"))->epp;
	    $this->username = $this->username ?: $this->config->username;
	    $this->password = $this->password ?: $this->config->password;
	    $this->instantiateEppClient();
	    $this->configureEppClientNamespaceCollection();
	    $this->connect();
    }

	/**
	 * @return void
	 */
	private function connect(): void
	{
		if (!$this->isConnected()) {
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
	 * @return void
	 */
	private function instantiateEppClient(): void
	{
		$uri = $this->uri ?: $this->config->endpoint;
		$uri = $uri . ":" . $this->config->port;

		$logger = new Logger(self::APP_NAME);
		$logger->pushHandler(new StreamHandler($this->logfile(), Logger::DEBUG));

		$connectionConfig = new StreamSocketConfig();
		$connectionConfig->uri = $uri;
		$connectionConfig->timeout = $this->timeout;
		$connectionConfig->context = [
			'ssl' => [
				'local_cert' =>  $this->config->cert_file,
			]
		];
		$connection = new StreamSocketConnection($connectionConfig, $logger);
		$this->client = new EPPClient($connection, $logger);
	}

	/**
     * configureEppClientNamespaceCollection
     * @return void
     */
    private function configureEppClientNamespaceCollection(): void
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