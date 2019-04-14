<?php

namespace CrCms\Microservice\Client\Facades;

use Illuminate\Support\Facades\Facade;
use CrCms\Microservice\Client\Service as ServiceInstance;

/**
 * @method static object call(string $service, $uri = '', array $params = [])
 * @method static bool status()
 * @method static mixed getContent()
 * @method static int getStatusCode()
 * @method static ServiceInstance driver(?string $driver = null)
 * @method static ServiceInstance connection(?string $name = null)
 *
 * Class Service
 */
class Service extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ServiceInstance::class;
    }
}
