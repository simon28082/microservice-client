<?php

namespace CrCms\Microservice\Client\Contracts;

/**
 * Interface ServiceDiscoverContract
 * @package CrCms\Microservice\Client\Contracts
 */
interface ServiceDiscoverContract
{
    /**
     * @param $service
     * @return array
     */
    public function services(string $service): array;
}