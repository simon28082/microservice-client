<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:31
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client\Contracts;

interface SelectorContract
{
    /**
     * @param string $service
     *
     * @return array
     */
    public function select(string $service): array;
}
