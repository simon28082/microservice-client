<?php

return [

    'default' => 'consul',

    'connections' => [
        'consul' => [
            'discovery' => [
                'host' => '192.168.1.12',
                'port' => 8500,
                'uri' => 'v1/catalog/service',
                'services' => [
                    'assistant',
                ],
            ],
            'driver' => [
                'name' => 'restful',
                'headers' => [
                    'User-Agent' => 'CRCMS-MICRO-SERVER PHP Client',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ],
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Connection pool selector
    |--------------------------------------------------------------------------
    |
    | Different selectors can be selected to select the connection in the connection pool
    | RandSelector: Randomly select an available selector
    | RingSelector: A ring selector to ensure scheduling equalization
    | ResidentSelector: Always use the same available selector
    | PopSelector: Swoole coroutines are used, each time an independently generated connection
    */

    'selector' => \CrCms\Foundation\MicroService\Client\Selectors\RandSelector::class,
];