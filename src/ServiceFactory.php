<?php

namespace CrCms\Microservice\Client;

use CrCms\Microservice\Client\Clients\Restful;
use CrCms\Microservice\Client\Contracts\ClientContract;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

/**
 * Class ServiceFactory.
 */
class ServiceFactory
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
     * @return ClientContract
     */
    public function make(string $driver): ClientContract
    {
        $config = $this->app->make('config')->get("microservice-client.clients.{$driver}");
        $options = $this->serviceConfig();

        switch ($config['name']) {
            case 'restful':
                return new Restful($this->app->make('client.manager'), $config, $options);
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['name']}]");
    }

    /**
     * @return array
     */
    protected function serviceConfig(): array
    {
        return $this->app->make('config')->get('microservice-client.service_options', []);
    }
}
