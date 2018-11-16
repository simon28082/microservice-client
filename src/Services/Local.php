<?php

namespace CrCms\Microservice\Client\Services;

use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use RangeException;

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
     * @param string $service
     * @return array
     */
    public function services(string $service): array
    {
        $result = Collection::make(
            $this->app->make('config')->get("microservice-client.connections.local.discover")
        )->groupBy('name')->get($service);

        if (is_null($result)) {
            throw new RangeException("The service[{$service}] not found");
        }

        return $result->toArray();
    }
}

