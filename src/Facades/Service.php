<?php

namespace CrCms\Foundation\MicroService\Client\Facades;

use Illuminate\Support\Facades\Facade;
use CrCms\Foundation\MicroService\Client\Service as ServiceInstance;

/**
 * @method static object call(string $name, ?string $uri = null, array $params = [])
 * @method static ServiceInstance driver(?string $driver = null)
 * @method static ServiceInstance connection(?string $name = null)
 *
 * Class Service
 * @package CrCms\Foundation\MicroService\Client\Facades
 */
class Service extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ServiceInstance::class;
    }
}