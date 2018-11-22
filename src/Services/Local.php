<?php

namespace CrCms\Microservice\Client\Services;

use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;
use OutOfBoundsException;

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
     * @var array
     */
    protected $services;

    /**
     * @var array
     */
    protected $config;

    /**
     * Local constructor.
     * @param Container $app
     * @param array $config
     */
    public function __construct(Container $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @param string $service
     * @return array
     */
    public function services(string $service): array
    {
        if (empty($this->services[$service])) {
            $services = $this->readServices();
            if (empty($services[$service])) {
                throw new OutOfBoundsException("The service: {$service} not found");
            }

            $this->services[$service] = $services[$service];
            unset($services);
        }

        return $this->services[$service];
    }

    /**
     * @return array
     */
    protected function readServices(): array
    {
        $path = $this->config['discover']['path'];
        if (!file_exists($path)) {
            throw new InvalidArgumentException("The file[{$path}] not found");
        }

        $content = file_get_contents($path);
        $services = json_decode($content, true);
        if (json_last_error() !== 0) {
            throw new UnexpectedValueException("The services resolve error");
        }

        return Collection::make($services)->groupBy('name')->toArray();
    }
}

