<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/25 6:31
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService\Client\Contracts;

interface SelectorContract
{
    /**
     * @param array $connections
     * @return array
     */
    public function select(array $connections): array;
}