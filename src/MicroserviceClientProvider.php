<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/07/02 19:15
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client;

use CrCms\Foundation\Client\ClientServiceProvider;
use CrCms\Microservice\Client\Contracts\SecretContract;
use CrCms\Microservice\Client\Contracts\SelectorContract;
use CrCms\Microservice\Client\Packer\Packer;
use CrCms\Microservice\Client\Selectors\RandSelector;
use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;
use CrCms\Microservice\Client\Services\Local;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

/**
 * Class MicroServiceProvider
 * @package CrCms\Foundation\Rpc
 */
class MicroserviceClientProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @var string
     */
    protected $namespaceName = 'microservice-client';

    /**
     * @var string
     */
    protected $packagePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

    /**
     * @return void
     */
    public function boot()
    {
        //move config path
        if ($this->isLumen()) {

        } else {
            $this->publishes([
                $this->packagePath . 'config/config.php' => config_path($this->namespaceName . ".php"),
            ]);
        }

        //event listen
        foreach ($this->app->make('config')->get('microservice-client.events', []) as $event) {
            Event::listen('microservice.call', $event . '@handle');
            Event::listen('microservice.call.failed', $event . '@failed');
        }
    }

    /**
     * @return void
     */
    public function register(): void
    {
        if ($this->isLumen()) {
            $this->app->configure($this->namespaceName);
        }

        //merge config
        $configFile = $this->packagePath . "config/config.php";
        if (file_exists($configFile)) $this->mergeConfigFrom($configFile, $this->namespaceName);

        $this->registerAlias();
        $this->registerServices();
        $this->registerCommands();

        //$this->app->register(ClientServiceProvider::class);
    }

    /**
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->singleton(ServiceFactory::class, function ($app) {
            return new ServiceFactory($app);
        });

        $this->app->singleton('microservice-client.selector', function ($app) {
            return new RandSelector($app['microservice-client.discover']);
        });

        $this->app->singleton('microservice-client.packer', function ($app) {
            return new Packer($app['encrypter'], $app['config']->get('microservice-client.encryption'));
        });

        $this->app->singleton('microservice-client.discover', function ($app) {
            return new Local($app, $app['config']->get('microservice-client.connections.local'));
        });
    }

    /**
     * @return bool
     */
    protected function isLumen(): bool
    {
        return class_exists(Application::class) && $this->app instanceof Application;
    }

    /**
     * @return void
     */
    protected function registerCommands(): void
    {
    }

    /**
     * @return void
     */
    protected function registerAlias(): void
    {
        $this->app->alias('microservice-client.discover', ServiceDiscoverContract::class);
        $this->app->alias('microservice-client.selector', SelectorContract::class);
        $this->app->alias('microservice-client.packer', Packer::class);
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            ServiceDiscoverContract::class,
            SelectorContract::class,
            ServiceFactory::class,
            Packer::class,
        ];
    }
}