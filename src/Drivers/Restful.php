<?php

namespace CrCms\Foundation\MicroService\Client\Drivers;

use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\MicroService\Client\Contracts\ServiceContract;

/**
 * Class Restful
 * @package CrCms\Foundation\MicroService\Client\Drivers
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
     * @var string
     */
    protected $method = 'get';

    /**
     * @var Manager
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * Restful constructor.
     * @param Manager $manager
     * @param array $config
     */
    public function __construct(Manager $manager, array $config = [])
    {
        $this->client = $manager;
        $this->config = $config;
        $this->addHeaders($config['headers'] ?? []);
    }

    /**
     * @param string $method
     * @return Restful
     */
    public function method(string $method): self
    {
        return $this->setMethod($method);
    }

    /**
     * @param string $method
     * @return Restful
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param array $headers
     * @return Restful
     */
    public function headers(array $headers): self
    {
        return $this->addHeaders($headers);
    }

    /**
     * @param array $headers
     * @return Restful
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
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
     * @return Manager
     */
    public function call(array $service, string $uri, array $params = []): Manager
    {
        return $this->client->connection([
            'name' => $service['ServiceName'],
            'driver' => $this->config['driver'],
            'host' => $service['ServiceAddress'],
            'port' => $service['ServicePort'],
            'settings' => $this->config['options'],
        ])->request($uri, ['headers' => $this->headers, 'method' => $this->method, 'payload' => $params]);
    }

    /**
     * @param string $key
     * @param string $password
     * @return ServiceContract
     */
    public function auth(string $key, string $password = ''): ServiceContract
    {
        $this->headers['Authorization'] = $key;
        return $this;
    }
}