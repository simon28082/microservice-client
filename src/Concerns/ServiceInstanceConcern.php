<?php

namespace CrCms\Foundation\MicroService\Client\Concerns;

use CrCms\Foundation\MicroService\Client\Service;

/**
 * Trait ServiceInstanceConcern
 * @package CrCms\Foundation\MicroService\Client\Concerns
 */
trait ServiceInstanceConcern
{
    /**
     * @return Service
     */
    public function service(): Service
    {
        return app(Service::class);
    }
}