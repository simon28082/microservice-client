<?php

namespace CrCms\Foundation\MicroService\Client;

use CrCms\Foundation\Client\Manager;
use CrCms\Foundation\MicroService\Client\Contracts\Selector;
use CrCms\Foundation\MicroService\Client\Contracts\ServiceDiscoverContract;
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
     * @var Selector
     */
    protected $selector;

    /**
     * @var Manager
     */
    protected $client;

    /**
     * ServiceDiscover constructor.
     * @param Application $app
     * @param Selector $selector
     * @param Manager $manager
     */
    public function __construct(Application $app, Selector $selector, Manager $manager)
    {
        $this->app = $app;
        $this->selector = $selector;
        $this->client = $manager;
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
        $config = $this->app->make('config')->get("rpc.connections.{$driver}.discovery");
        $this->client->connection($driver);
        try {
            $content = $this->client->request($config['uri'] . '/' . $service, ['method' => 'get'])->getContent();
            // @todo 这里还需要其它的判断，判断Client是否OK，JSON解析是否OK
            $content = json_decode($content, true);
            if (json_last_error() !== 0) {
                throw new UnexpectedValueException("JSON parse error");
            }
            return collect($content)->mapWithKeys(function ($item) {
                return [$item['ServiceID'] => $item];
            })->toArray();
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            $this->client->close();
        }
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
        return $this->app->make('config')->get('rpc.default');
    }
}