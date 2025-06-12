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

/**
 * Class Socket
 * Entry Point for the EPP Client
 *
 * @package Novatech\Epp
 * @author Bona Philippe Lukengu<lukengup@aim.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

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

	const APP_NAME = 'epp_client';

	/**
	 * @param string|null $username
	 * @param string|null $password
	 * @param string|null $uri
	 * @param int $timeout
	 */
	public function __construct(
		private ?string $username = null,
		private ?string $password = null,
		private ?string  $uri = null,
		private int $timeout = 10
	) {
		$this->config = new Config(require "config/epp.config.php");
		$this->username = $this->username ?: $this->config->epp->username;
		$this->password = $this->password ?: $this->config->epp->password;
		$this->uri = $this->uri ?: $this->config->epp->endpoint . ':' . $this->config->epp->port;


		$this->initializeEppClient();
		$this->setupNamespaceCollection();
		$this->connect();
	}

	/**
	 * @return void
	 */
	private function connect(): void
	{
		$this->client->connect();
		$request = new LoginRequest($this->client);
		$request->setLogin($this->username)
			->setPassword($this->password)
			->setLanguage('en')
			->setProtocolVersion('1.0');

		$this->execute($request);
	}

	/**
	 * @return void
	 */
	private function initializeEppClient(): void
	{
		$logger = new Logger(self::APP_NAME);
		$logger->pushHandler(new StreamHandler($this->logfile(), Logger::DEBUG));

		$connectionConfig = new StreamSocketConfig();
		$connectionConfig->uri = $this->uri;
		$connectionConfig->timeout = $this->timeout;
		$connectionConfig->context = [
			'ssl' => [
				'local_cert' => $this->config->epp->cert_file,
			],
		];
		$connection = new StreamSocketConnection($connectionConfig, $logger);
		$this->client = new EPPClient($connection, $logger);
	}

	/**
	 * @return void
	 */
	private function setupNamespaceCollection(): void
	{
		$namespaceCollection = $this->client->getNamespaceCollection();

		$namespaceCollection->offsetSet(
			NamespaceCollection::NS_NAME_ROOT,
			'urn:ietf:params:xml:ns:epp-1.0'
		);
		$namespaceCollection->offsetSet(
			NamespaceCollection::NS_NAME_CONTACT,
			'urn:ietf:params:xml:ns:contact-1.0'
		);
		$namespaceCollection->offsetSet(
			NamespaceCollection::NS_NAME_HOST,
			'urn:ietf:params:xml:ns:host-1.0'
		);
		$namespaceCollection->offsetSet(
			NamespaceCollection::NS_NAME_DOMAIN,
			'urn:ietf:params:xml:ns:domain-1.0'
		);
	}

	/**
	 * @param RequestInterface $request
	 * @return ResponseInterface
	 */
	public function execute(RequestInterface $request): ResponseInterface
	{
		if (!$this->isConnected()) {
			$this->connect();
		}
		return $this->client->send($request);
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
	private function logout(): void
	{
		$this->execute(new LogoutRequest($this->client));
		$this->client->disconnect();
	}

	/**
	 * @return false|resource
	 */
	private function logfile()
	{
		return fopen($this->config->epp->log_file, 'a');
	}
}
