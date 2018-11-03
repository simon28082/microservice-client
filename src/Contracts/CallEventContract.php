<?php

namespace CrCms\Foundation\MicroService\Client\Contracts;

use CrCms\Foundation\MicroService\Client\Service;
use Exception;

/**
 * Interface CallEventContract
 * @package CrCms\Foundation\MicroService\Client\Contracts
 */
interface CallEventContract
{
    /**
     * @param Service $service
     * @param array $serverInfo
     * @return void
     */
    public function handle(Service $service, array $serverInfo): void;

    /**
     * @param Service $service
     * @param Exception $exception
     * @param array $serverInfo
     * @return void
     */
    public function failed(Service $service, Exception $exception, array $serverInfo): void;
}