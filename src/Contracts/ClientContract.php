<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/6/23 18:36
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client\Contracts;

interface ClientContract
{
    /**
     * @param array        $service
     * @param string       $uri
     * @param array|string $params
     *
     * @return ClientContract
     */
    public function call(array $service, $params = []): self;

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @return mixed
     */
    public function getContent();
}
