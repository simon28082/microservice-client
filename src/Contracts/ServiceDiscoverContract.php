<?php

namespace CrCms\Microservice\Client\Contracts;

/**
 * Interface ServiceDiscoverContract
 * @package CrCms\Microservice\Client\Contracts
 */
interface ServiceDiscoverContract
{
    /**
     * @param array $config
     * @return array
     */
    public function services(array $config): array;
}