<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client;

use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\ConnectionPool\Exceptions\RequestException;
use CrCms\Microservice\Client\Contracts\ServiceContract;
use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;
use CrCms\Microservice\Client\Exceptions\ServiceException;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException, BadMethodCallException, DomainException;

/**
 * Class Service
 * @package CrCms\Microservice\Client
 */
class Service
{
    /**
     * @var ServiceContract
     */
    protected $service;

    /**
     * @var ServiceDiscoverContract
     */
    protected $serviceDiscover;

    /**
     * 重试次数
     *
     * @var int
     */
    protected $retry = 0;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var object
     */
    protected $data;

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
     * @var Container
     */
    protected $app;

    /**
     * Service constructor.
     * @param Container $container
     * @param ServiceDiscoverContract $serviceDiscover
     * @param ServiceFactory $factory
     */
    public function __construct(Container $container, ServiceDiscoverContract $serviceDiscover, ServiceFactory $factory)
    {
        $this->app = $container;
        $this->serviceDiscover = $serviceDiscover;
        $this->factory = $factory;
        $this->driver();
        $this->connection();
    }

    /**
     * @param null|string $name
     * @return $this
     */
    public function connection(?string $name = null)
    {
        $this->connection = $name ? $name : $this->app->make('config')->get('microservice-client.default');

        return $this;
    }

    /**
     * @param string $name
     * @param null|string $uri
     * @param array $params
     * @return object
     */
    public function call(string $name, ?string $uri = null, array $params = [])
    {
        $this->whileGetConnection(
            $this->serviceDiscover->discover($name, $this->connection),
            $uri, $params
        );

        $this->data = new ServiceData($this->service()->getContent());

        return $this->data;
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
     * @param array $params
     * @return string
     */
    public function secret(array $params): string
    {
        return hash_hmac(
            'ripemd256', serialize($params),
            (string)$this->app->make('config')->get('microservice-client.secret')
        );
    }

    /**
     * 循环获取连接，直到非异常连接
     *
     * @param array $service
     * @param string $uri
     * @param array $params
     * @param int $depth
     * @return ServiceContract
     */
    protected function whileGetConnection(array $service, string $uri, array $params = [], int $depth = 1): ServiceContract
    {
        try {
            return $this->service()->auth($this->secret($params))->call($service, ['call' => $uri, 'data' => $params]);
        } catch (ConnectionException $exception) {
            if ($depth > $this->retry) {
                $this->throwException($exception);
            }
            return $this->whileGetConnection($service, $uri, $params, $depth += 1);
        } catch (\Exception $exception) {
            $this->throwException($exception);
        } finally {
            /* 服务上报，事件触发 */
            $serverInfo = compact('service', 'uri', 'params');
            $callParams = isset($exception) ? ['microservice.call.failed', [$this, $exception, $serverInfo]] : ['microservice.call', [$this, $serverInfo]];

            call_user_func_array([$this->app->make('events'), 'fire'], $callParams);
        }
    }

    /**
     * @param $exception
     */
    protected function throwException($exception)
    {
        throw new ServiceException($exception);
    }

    /**
     * @return ServiceContract
     */
    public function service(): ServiceContract
    {
        if (!$this->service instanceof ServiceContract) {
            $this->service = $this->factory->make($this->driver);
        }

        return $this->service;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data->{$name};
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->service(), $name)) {
            $result = call_user_func_array([$this->service(), $name], $arguments);
            if ($result instanceof ServiceContract) {
                $this->service = $result;
                return $this;
            }

            return $result;
        }

        return $this->call($name, ...$arguments);
    }
}