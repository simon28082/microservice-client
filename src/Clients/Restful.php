<?php

namespace CrCms\Microservice\Client\Clients;

use CrCms\Foundation\Client\ClientManager;
use CrCms\Microservice\Client\Contracts\ServiceContract;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Restful
 * @package CrCms\Microservice\Client\Drivers
 */
class Restful implements ServiceContract
{
    /**
     * @var array
     */
    protected $headers = [
        'User-Agent' => 'CRCMS-MICRO-SERVER PHP Client',
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $method = 'post';

    /**
     * @var array
     */
    protected $defaultConfig = [
        'timeout' => 1,
    ];

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var ClientManager
     */
    protected $client;

    /**
     * Restful constructor.
     * @param ClientManager $manager
     * @param array $config
     */
    public function __construct(ClientManager $manager, array $config)
    {
        $this->client = $manager;
        $this->config = $config;
        $this->addHeaders($config['headers'] ?? []);
    }

    /**
     * @param array $headers
     * @return Restful
     */
    public function addHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @param array $service
     * @param string $uri
     * @param array $params
     * @return Restful
     */
    public function call(array $service, array $params = []): ServiceContract
    {
        $this->client->connection([
            'name' => $service['name'],
            'driver' => $this->config['driver'],
            'host' => $service['host'],
            'port' => $service['port'],
            'settings' => array_merge($this->defaultConfig, $this->config['options']),
        ])->handle('/', ['headers' => $this->headers, 'method' => $this->method, 'payload' => $params]);

        $this->statusCode = $this->client->getStatusCode();
        $this->content = $this->client->getContent();

        $this->client->disconnection();

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}