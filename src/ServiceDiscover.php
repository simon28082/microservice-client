<?php

namespace CrCms\Foundation\MicroService\Client;

use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\MicroService\Client\Contracts\SelectorContract;
use CrCms\Foundation\MicroService\Client\Contracts\ServiceDiscoverContract;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Application;
use Exception;
use UnexpectedValueException;

/**
 * Class ServiceDiscover
 * @package CrCms\Foundation\MicroService\Client
 */
class ServiceDiscover implements ServiceDiscoverContract
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var SelectorContract
     */
    protected $selector;

    /**
     * @var Manager
     */
    protected $client;

    /**
     * @var Repository
     */
    protected $cache;

    /**
     * ServiceDiscover constructor.
     * @param Application $app
     * @param SelectorContract $selector
     * @param Manager $manager
     * @param Repository $cache
     */
    public function __construct(Application $app, SelectorContract $selector, Manager $manager)
    {
        $this->app = $app;
        $this->selector = $selector;
        $this->client = $manager;
        $this->cache = $app->make(Repository::class);
    }

    /**
     * @param string $service
     * @param null|string $driver
     * @return array
     * @throws Exception
     */
    public function discover(string $service, ?string $driver = null): array
    {
        $driver = $driver ? $driver : $this->defaultDriver();
        $serviceKey = $this->serviceKey($service, $driver);

        if (empty($this->services[$serviceKey])) {
            $this->services[$serviceKey] = $this->services($service, $driver);
        }

        return $this->selector->select($this->services[$serviceKey]);
    }

    /**
     * @param string $service
     * @param string $driver
     * @return array
     * @throws Exception
     */
    protected function services(string $service, string $driver): array
    {
        $services = $this->builtInServices($service, $driver);
        if (empty($services)) {
            $services = $this->discoverServices($service, $driver);
        }

        return $services;
    }

    /**
     * @param string $service
     * @param string $driver
     * @return array
     * @throws Exception
     */
    protected function discoverServices(string $service, string $driver): array
    {
        $serviceKey = $this->serviceKey($service, $driver);
        if ($this->cache->has($serviceKey)) {
            return $this->cache->get($serviceKey);
        }

        $config = $this->app->make('config')->get("micro-service-client.connections.{$driver}.discover");

        $this->client->connection([
            'driver' => $config['driver'],
            'host' => $config['host'],
            'port' => $config['port'],
        ], false);

        try {
            $content = $this->client->request($config['uri'] . '/' . $service, ['method' => 'get'])->getContent();
            // @todo 这里还需要其它的判断，判断Client是否OK，JSON解析是否OK
            $content = json_decode($content, true);
            if (json_last_error() !== 0) {
                throw new UnexpectedValueException("JSON parse error");
            }
            $result = collect($content)->mapWithKeys(function ($item) {
                return [$item['ServiceID'] => $item];
            })->toArray();

            $this->cache->put($serviceKey, $result, $this->app->make('config')->get("micro-service-client.discover_refresh_time", 5));

            return $result;
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            $this->client->close();
        }
    }

    /**
     * @param string $service
     * @param string $driver
     * @return array
     */
    protected function builtInServices(string $service, string $driver): array
    {
        $services = $this->app->make('config')->get("micro-service-client.connections.{$driver}.services");
        if (count($services) !== count($services, COUNT_RECURSIVE)) {
            return $services;
        }
        return [];
    }

    /**
     * @param string $service
     * @param string $driver
     * @return string
     */
    protected function serviceKey(string $service, string $driver): string
    {
        return $service . '_' . $driver;
    }

    /**
     * @return string
     */
    protected function defaultDriver(): string
    {
        return $this->app->make('config')->get('micro-service-client.default');
    }
}