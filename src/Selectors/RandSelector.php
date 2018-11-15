<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:47
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client\Selectors;

use CrCms\Microservice\Client\Contracts\SelectorContract;

/**
 * Class RandSelector
 * @package CrCms\Microservice\Client\Selectors
 */
class RandSelector implements SelectorContract
{
    /**
     * @param array $services
     * @return array
     */
    public function select(array $services): array
    {
        return $services[array_rand($services)];
    }
}