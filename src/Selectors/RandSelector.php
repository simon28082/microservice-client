<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client\Selectors;

use CrCms\Microservice\Client\Contracts\SelectorContract;
use CrCms\Microservice\Client\Contracts\ServiceDiscoverContract;

/**
 * Class RandSelector
 * @package CrCms\Microservice\Client\Selectors
 */
class RandSelector implements SelectorContract
{
    /**
     * @param ServiceDiscoverContract $discover
     * @return array
     */
    public function select(ServiceDiscoverContract $discover): array
    {
        $services = $discover->services();
        return $services[array_rand($services)];
    }
}