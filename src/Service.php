<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/2 6:14
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService\Client;

use function CrCms\Foundation\App\Helpers\is_serialized;
use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use BadMethodCallException;
use CrCms\Foundation\MicroService\Client\Contracts\ServiceContract;
use CrCms\Foundation\MicroService\Client\Contracts\ServiceDiscoverContract;
use CrCms\Foundation\MicroService\Client\ServiceFactory;
use Illuminate\Contracts\Container\Container;
use stdClass;
use InvalidArgumentException;
use DomainException;

/**
 * Class Service
 * @package CrCms\Foundation\MicroService\Client
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
    protected $retry = 3;

    /**
     * @var Manager
     */
    protected $client;

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
        $this->connection = $name ? $name : $this->app->make('config')->get('micro-service-client.default');

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
        $this->client = $this->whileGetConnection(
            $this->serviceDiscover->discover($name, $this->connection),
            $uri, $params
        );

        $this->resolveData($this->client->getContent());

        return $this->getData();
    }

    /**
     * @param null|string $driver
     * @return Service
     */
    public function driver(?string $driver = null): Service
    {
        $driver = $driver ? $driver : $this->app->make('config')->get('micro-service-client.client');

        $connections = array_keys($this->app->make('config')->get('micro-service-client.clients'));
        if (!in_array($driver, $connections, true)) {
            throw new DomainException("The Driver[{$driver}] not exists");
        }

        $this->driver = $driver;

        return $this;
    }

    /**
     * @param mixed $data
     */
    protected function resolveData($data): void
    {
        if ((bool)($newData = json_decode($data)) && json_last_error() === 0) {
            $this->data = $newData;
        } else {
            $this->data = null;
        }
    }

    /**
     * @param array $params
     * @return string
     */
    public function secret(array $params): string
    {
        return hash_hmac(
            'ripemd256', serialize($params),
            (string)$this->app->make('config')->get('micro-service-client.secret')
        );
    }

    /**
     * @return object
     */
    public function getData(): object
    {
        return $this->data;
    }

    /**
     * @return Manager
     */
    public function getClient(): Manager
    {
        return $this->client;
    }

    /**
     * 循环获取连接，直到非异常连接
     *
     * @param array $service
     * @param string $uri
     * @param array $params
     * @param int $depth
     * @return Manager
     */
    protected function whileGetConnection(array $service, string $uri, array $params = [], int $depth = 1): Manager
    {
        try {
            return $this->service()->auth($this->secret($params))->call($service, $uri, $params);
        } catch (ConnectionException $exception) {
            if ($depth > $this->retry) {
                throw $exception;
            }
            return $this->whileGetConnection($service, $uri, $params, $depth += 1);
        }
    }

    /**
     * @return ServiceContract
     */
    protected function service(): ServiceContract
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
        if (isset($this->data->{$name})) {
            return $this->data->{$name};
        }

        throw new InvalidArgumentException("The attribute[{$name}] is not exists");
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
        }

        return $this->call($name, ...$arguments);
        //throw new BadMethodCallException("The method[{$name}] is not exists");
    }
}