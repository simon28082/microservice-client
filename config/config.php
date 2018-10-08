<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default connection name
    |--------------------------------------------------------------------------
    |
    */

    'default' => 'consul',

    /*
    |--------------------------------------------------------------------------
    | Service Connections
    |--------------------------------------------------------------------------
    |
    | The default remote discovery of available services will be cached for efficiency
    |
    */

    'connections' => [
        'consul' => [
            'discover' => [
                'driver' => 'http',
                'host' => env('SERVICE_DISCOVER_HOST', null),
                'port' => env('SERVICE_DISCOVER_PORT', 8500),
                'uri' => 'v1/catalog/service',
            ],
            'services' => [
                'test',
            ],
            'client' => env('SERVICE_CLIENT_DRIVER', 'restful'),//or
            /*'client' => [
                'name' => 'restful'
            ]*/
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service discovery refresh time(Minute)
    |--------------------------------------------------------------------------
    |
    | The default remote discovery of available services will be cached for efficiency
    |
    */

    'discover_refresh_time' => 5,

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