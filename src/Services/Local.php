<?php

namespace CrCms\Microservice\Client\Services;

use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;
use Illuminate\Contracts\Container\Container;

/**
 * Class Local
 * @package CrCms\Microservice\Client\Services
 */
class Local implements ServiceDiscoverContract
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * Local constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @return array
     */
    public function services(): array
    {
        return $this->app->make('config')->get("microservice-client.connections.local.discover");
    }
}

