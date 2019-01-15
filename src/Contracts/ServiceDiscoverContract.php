<?php

namespace CrCms\Microservice\Client\Contracts;

/**
 * Interface ServiceDiscoverContract.
 */
interface ServiceDiscoverContract
{
    /**
     * @param $service
     *
     * @return array
     */
    public function services(string $service): array;
}
