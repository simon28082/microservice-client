<?php

return [

    'default' => 'consul',

    'connections' => [
        'consul' => [
            'discover' => [
                'driver' => 'http',
                'host' => '192.168.1.106',
                'port' => 8500,
                'uri' => 'v1/catalog/service',
            ],
            'services' => [
                'test',
            ],
            'client' => 'restful',//or
            /*'client' => [
                'name' => 'restful'
            ]*/
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Services selector
    |--------------------------------------------------------------------------
    |
    | Different selectors can be selected to select connections for multiple services in service discovery
    | RandSelector: Randomly select an available selector
    | RingSelector: A ring selector to ensure scheduling equalization
    | ResidentSelector: Always use the same available selector
    | PopSelector: Swoole coroutines are used, each time an independently generated connection
    */

    'selector' => \CrCms\Foundation\MicroService\Client\Selectors\RandSelector::class,
];