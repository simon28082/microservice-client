<?php

namespace CrCms\Microservice\Client\Contracts;

/**
 * Interface ServiceDiscoverContract
 * @package CrCms\Microservice\Client\Contracts
 */
interface ServiceDiscoverContract
{
    /**
     * @return array
     */
    public function services(): array;
}