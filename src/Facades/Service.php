<?php

namespace CrCms\Microservice\Client\Facades;

use Illuminate\Support\Facades\Facade;
use CrCms\Microservice\Client\Service as ServiceInstance;

/**
 * @method static object call(string $name, ?string $uri = null, array $params = [])
 * @method static ServiceInstance driver(?string $driver = null)
 * @method static ServiceInstance connection(?string $name = null)
 *
 * Class Service
 * @package CrCms\Microservice\Client\Facades
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