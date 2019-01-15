<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/4 6:05
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Client\Selectors;

use CrCms\Microservice\Client\Contracts\SelectorContract;

/**
 * Class ResidentSelector.
 */
class ResidentSelector implements SelectorContract
{
    /**
     * @param array $connections
     *
     * @return array
     */
    public function select(array $connections): array
    {
        return $connections[0];
    }
}
