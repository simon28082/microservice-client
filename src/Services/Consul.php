<?php

namespace CrCms\Microservice\Client\Services;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;
use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;

/**
 * Class Consul.
 */
class Consul implements ServiceDiscoverContract
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
     * @var Repository
     */
    protected $cache;

    /**
     * @var ClientManager
     */
    protected $client;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * Local constructor.
     *
     * @param Container $app
     * @param array     $config
     */
    public function __construct(Container $app, array $config, ClientManager $clientManager, Repository $cache)
    {
        $this->app = $app;
        $this->config = $config;
        $this->client = $clientManager;
        $this->cache = $cache;
        $this->cacheKey = $this->cacheKey();
    }

    /**
     * @param string $service
     *
     * @return array
     */
    public function services(string $service): array
    {
        if (empty($this->services[$service])) {
            $this->services = $this->all();
        }

        if (! empty($this->services[$service])) {
            return $this->services[$service];
        }

        throw new RangeException("The service[{$service}] not found");
    }

    /**
     * @return array
     */
    protected function net(): array
    {
        $this->client->connection([
            'driver' => 'http',
            'host'   => $this->config['connections']['consul']['discover']['host'],
            'port'   => $this->config['connections']['consul']['discover']['port'],
        ], false);

        try {
            $content = $this->client->handle($this->config['connections']['consul']['discover']['uri'], ['method' => 'get'])->getContent();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->client->disconnection();
        }

        $content = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException('JSON parse error');
        }

        return $content;
    }

    /**
     * @return array
     */
    protected function all(): array
    {
        $services = $this->cache->get($this->cacheKey);
        if (! empty($services)) {
            return $services;
        }

        $services = $this->format($this->net());

        $this->cache->put(
            $this->cacheKey, $services,
            $this->config['discover_refresh_time'] ?? 5
        );

        return $services;
    }

    /**
     * @param array $services
     *
     * @return array
     */
    protected function format(array $services): array
    {
        return Collection::make($services)->map(function (array $service) {
            return [
                'id'   => $service['ID'],
                'name' => $service['Service'],
                'host' => $service['Address'],
                'port' => $service['Port'],
            ];
        })->groupBy('name')->toArray();
    }

    /**
     * @return string
     */
    protected function cacheKey(): string
    {
        return 'microservice-client-consul';
    }
}
