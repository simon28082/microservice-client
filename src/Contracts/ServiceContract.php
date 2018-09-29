<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/23 18:36
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService\Client\Contracts;

use CrCms\Foundation\Client\Manager;

interface ServiceContract
{
    /**
     * @param array $service
     * @param string $uri
     * @param array $params
     * @return Manager
     */
    public function call(array $service, string $uri, array $params = []): Manager;

    /**
     * @param string $key
     * @param string $passowrd
     * @return ServiceContract
     */
    public function auth(string $key, string $passowrd = ''): ServiceContract;
}