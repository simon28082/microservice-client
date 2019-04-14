<?php

namespace CrCms\Microservice\Client;

use InvalidArgumentException;
use CrCms\Microservice\Client\Services\Local;
use CrCms\Microservice\Client\Services\Swarm;
use Illuminate\Contracts\Container\Container;
use CrCms\Microservice\Client\Services\Consul;
use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;

/**
 * Class ServiceConnnectionFactory.
 */
class ServiceConnnectionFactory
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * ServiceFactory constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->app = $container;
    }

    /**
     * @param string $driver
     *
     * @return ServiceDiscoverContract
     */
    public function make(string $driver): ServiceDiscoverContract
    {
        $connections = $this->allConnections();

        switch ($driver) {
            case 'local':
                return new Local($this->app, $connections);
            case 'swarm':
                return new Swarm($this->app, $connections,
                    $this->app['client.manager'],
                    $this->app['cache']->store());
            case 'consul':
                return new Consul($this->app, $connections,
                    $this->app['client.manager'],
                    $this->app['cache']->store());
        }

        throw new InvalidArgumentException("Unsupported driver [{$driver}]");
    }

    /**
     * @return array
     */
    protected function allConnections(): array
    {
        return $this->app->make('config')->get('microservice-client');
    }
}
