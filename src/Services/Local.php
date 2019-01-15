<?php

namespace CrCms\Microservice\Client\Services;

use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use OutOfBoundsException;
use UnexpectedValueException;

/**
 * Class Local.
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
     *
     * @param Container $app
     * @param array     $config
     */
    public function __construct(Container $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @param string $service
     *
     * @return array
     */
    public function services(string $service): array
    {
        if (empty($this->services[$service])) {
            $services = $this->all();
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
    protected function all(): array
    {
        $path = $this->config['connections']['local']['discover']['path'];
        if (!file_exists($path)) {
            throw new InvalidArgumentException("The file[{$path}] not found");
        }

        $content = file_get_contents($path);
        $services = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException('The services resolve error');
        }

        return Collection::make($services)->map(function (array $service, int $key) {
            if (empty($service['id'])) {
                $service['id'] = $key;
            }

            if (empty($service['port'])) {
                $service['port'] = $this->config['default_port'];
            }

            return $service;
        })->groupBy('name')->toArray();
    }
}
