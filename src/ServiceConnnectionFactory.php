<?php

namespace CrCms\Microservice\Client;

use CrCms\Microservice\Client\Contracts\ClientContract;
use CrCms\Microservice\Client\Clients\Restful;
use CrCms\Microservice\Client\Services\Local;
use CrCms\Microservice\Client\Services\Swarm;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

/**
 * Class ServiceConnnectionFactory
 * @package CrCms\Foundation\Rpc\Client
 */
class ServiceConnnectionFactory
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
     * @return ClientContract
     */
    public function make(string $driver): ClientContract
    {
        $connections = $this->allConnections();

        switch ($driver) {
            case 'local':
                return new Local($this->app, $connections);
            case 'swarm':
                return new Swarm($this->app, $connections,
                    $this->app['client.manager'],
                    $this->app['cache']);
                break;
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['name']}]");
    }

    /**
     * @return array
     */
    protected function allConnections(): array
    {
        return $this->app->make('config')->get('microservice-client.connections');
    }
}