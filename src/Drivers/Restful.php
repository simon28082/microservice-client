<?php

namespace CrCms\Foundation\MicroService\Client\Drivers;

use CrCms\Foundation\Client\ClientManager;
use CrCms\Foundation\MicroService\Client\Contracts\ServiceContract;
use Psr\Http\Message\ResponseInterface;

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
     * @var array
     */
    protected $config;

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
     * @var Connection
     */
    //protected $connection;

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
     * @return Restful
     */
    public function call(array $service, string $uri, array $params = []): ServiceContract
    {
        $this->client->connection([
            'name' => $service['ServiceName'],
            'driver' => $this->config['driver'],
            'host' => $service['ServiceAddress'],
            'port' => $service['ServicePort'],
            'settings' => array_merge($this->defaultConfig, $this->config['options']),
        ])->handle($this->resolveUri($uri), ['headers' => $this->headers, 'method' => $this->method, 'payload' => $params]);

        $this->statusCode = $this->client->getStatusCode();
        $this->content = $this->resolveData($this->client->getContent());

        $this->client->disconnection();

        return $this;
    }

    /**
     * @param $data
     * @return mixed|null
     */
    protected function resolveData($data)
    {
        if ((bool)($newData = json_decode($data)) && json_last_error() === 0) {
            return $newData;
        } else {
            return null;
        }
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

    /**
     * @param string $uri
     * @return string
     */
    protected function resolveUri(string $uri): string
    {
        if (strpos($uri, '.')) {
            $data = explode('.', $uri);
            if (in_array(strtolower($data[0]), ['get', 'post', 'put', 'patch', 'update', 'delete'], true)) {
                $this->setMethod($data[0]);
                return $data[1];
            }
        }

        return $uri;
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