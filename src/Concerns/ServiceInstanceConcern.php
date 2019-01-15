<?php

namespace CrCms\Microservice\Client\Concerns;

use CrCms\Microservice\Client\Service;

/**
 * Trait ServiceInstanceConcern.
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
