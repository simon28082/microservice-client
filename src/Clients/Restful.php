<?php

namespace CrCms\Microservice\Client\Clients;

use CrCms\Foundation\Client\ClientManager;
use CrCms\Microservice\Client\Contracts\ClientContract;

/**
 * Class Restful.
 */
class Restful implements ClientContract
{
    /**
     * @var array
     */
    protected $headers = [
        'User-Agent'      => 'CRCMS-Microservice PHP Client',
        'Accept-Encoding' => 'gzip,deflate,br',
        'Connection'      => 'keep-alive',
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
     * @var array
     */
    protected $options;

    /**
     * Restful constructor.
     *
     * @param ClientManager $manager
     * @param array         $config
     * @param array         $options
     */
    public function __construct(ClientManager $manager, array $config, array $options)
    {
        $this->client = $manager;
        $this->config = $config;
        $this->options = $options;
    }

    /**
     * @param array        $service
     * @param string       $uri
     * @param array|string $params
     *
     * @return Restful
     */
    public function call(array $service, $params = []): ClientContract
    {
        $this->client->connection([
            'name'     => $service['name'],
            'driver'   => $this->config['driver'],
            'host'     => $service['host'],
            'port'     => $service['port'],
            'settings' => array_merge($this->defaultConfig, $this->config['options'] ?? [], $this->options[$service['name']] ?? []),
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
