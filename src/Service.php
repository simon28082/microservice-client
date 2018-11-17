<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client;

use CrCms\Microservice\Client\Contracts\SecretContract;
use CrCms\Microservice\Client\Contracts\SelectorContract;
use CrCms\Microservice\Client\Exceptions\ServiceException;
use GuzzleHttp\Promise\Promise;
use Illuminate\Contracts\Container\Container;
use DomainException, UnexpectedValueException;
use Exception;

/**
 * Class Service
 * @package CrCms\Microservice\Client
 */
class Service
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var SelectorContract
     */
    protected $selector;

    /**
     * @var SecretContract
     */
    protected $secret;

    /**
     * @var ServiceData|null
     */
    protected $content;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var ServiceFactory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $driver;

    /**
     * @var string
     */
    protected $connection;

    /**
     * @var Promise
     */
    protected $promise;

    /**
     * Service constructor.
     * @param Container $container
     * @param SecretContract $secret
     * @param SelectorContract $selector
     * @param ServiceFactory $factory
     */
    public function __construct(Container $container, SecretContract $secret, SelectorContract $selector, ServiceFactory $factory)
    {
        $this->app = $container;
        $this->factory = $factory;
        $this->selector = $selector;
        $this->secret = $secret;
        $this->driver();
        $this->connection();
    }

    /**
     * @param string $service
     * @param string $uri
     * @param array $params
     * @return mixed
     */
    public function call(string $service, $uri = '', array $params = [])
    {
        return $this->execute($service, $uri, $params);
    }

    /**
     * @param string $service
     * @param string $uri
     * @param array $params
     * @return Promise
     */
    public function callAsync(string $service, $uri = '', array $params = []): Promise
    {
        $this->promise = new Promise();

        try {
            $this->execute($service, $uri, $params);
            $this->promise->resolve($this);
        } catch (ServiceException $exception) {
            $this->promise->reject($exception);
        }

        return $this->promise;
    }

    /**
     * @param string $service
     * @param string|array $uri
     * @param array $params
     * @return object
     */
    protected function execute(string $service, $uri = '', array $params = [])
    {
        if (is_array($uri) || empty($uri)) {
            $params = $uri ? $uri : [];
            $url = explode('.', $service);
            $service = array_shift($url);
            $uri = implode('.', $url);
        }

        $data = $params ? $this->secret->encrypt($params) : $params;

        try {
            $client = $this->factory->make($this->driver)->call($this->selector->select($service), array_merge(['call' => $uri], $data));
        } catch (Exception $exception) {
            throw new ServiceException($exception);
        } finally {
            /* 服务上报，事件触发 */
            $serverInfo = compact('service', 'uri', 'params');
            $callParams = isset($exception) ? ['microservice.call.failed', [$this, $exception, $serverInfo]] : ['microservice.call', [$this, $serverInfo]];
            $this->app['events']->dispatch(...$callParams);
        }

        $this->statusCode = $client->getStatusCode();
        $content = $this->parseContent($client->getContent());
        $this->content = $content ? new ServiceData($content) : null;

        return $this->content;
    }

    /**
     * @return bool
     */
    public function status(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @return ServiceData|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param null|string $driver
     * @return Service
     */
    public function driver(?string $driver = null): Service
    {
        $driver = $driver ? $driver : $this->app->make('config')->get('microservice-client.client');

        $connections = array_keys($this->app->make('config')->get('microservice-client.clients'));
        if (!in_array($driver, $connections, true)) {
            throw new DomainException("The Driver[{$driver}] not exists");
        }

        $this->driver = $driver;

        return $this;
    }

    /**
     * @param null|string $name
     * @return Service
     */
    public function connection(?string $name = null): self
    {
        $this->connection = $name ? $name : $this->app->make('config')->get('microservice-client.connection');

        return $this;
    }

    /**
     * @param $content
     * @return array
     */
    protected function parseContent($content)
    {
        if (!empty($content)) {
            $parsedData = json_decode($content, true);
            if (json_last_error() !== 0) {
                throw new UnexpectedValueException("The raw data error");
            }
            $content = $parsedData ? $this->secret->decrypt($parsedData) : null;
        }

        return $content;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (is_null($this->content)) {
            return null;
        }

        return $this->content->data($name);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->call($name, ...$arguments);
    }
}