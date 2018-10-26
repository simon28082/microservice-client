<?php

namespace CrCms\Foundation\MicroService\Client\Traits;

use CrCms\Foundation\MicroService\Client\Service;

/**
 * Class ServiceInstanceTrait
 * @package CrCms\Foundation\MicroService\Client\Traits
 */
class ServiceInstanceTrait
{
    /**
     * @var Service
     */
    //protected $service;

    /**
     * @return Service
     */
    public function service(): Service
    {
        return app(Service::class);
    }
}