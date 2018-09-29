<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018/7/4 5:36
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Foundation\MicroService\Client\Selectors;

use CrCms\Foundation\MicroService\Client\Contracts\SelectorContract;

/**
 * Class PopSelector
 * @package CrCms\Foundation\MicroService\Client\Selectors
 */
class PopSelector implements SelectorContract
{
    /**
     * @param array $connections
     * @return array
     */
    public function select(array $connections): array
    {
        return array_pop($connections);
    }
}