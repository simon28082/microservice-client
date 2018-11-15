<?php

namespace CrCms\Microservice\Client;

use CrCms\Microservice\Client\Contracts\ServiceContract;
use CrCms\Microservice\Client\Drivers\Restful;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

/**
 * Class ServiceFactory
 * @package CrCms\Foundation\Rpc\Client
 */
class ServiceFactory
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * ServiceFactory constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->app = $container;
    }

    /**
     * @param string $driver
     * @return ServiceContract
     */
    public function make(string $driver): ServiceContract
    {
        $config = $this->app->make('config')->get("microservice-client.clients.{$driver}");

        switch ($config['name']) {
            case 'restful':
                return new Restful($this->app->make('client.manager'), $config);
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['name']}]");
    }
}