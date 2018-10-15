<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default connection name
    |--------------------------------------------------------------------------
    |
    */

    'default' => env('SERVICE_CONNECTION_DRIVER', 'consul'),

    /*
    |--------------------------------------------------------------------------
    | Default client driver
    |--------------------------------------------------------------------------
    |
    */

    'client' => env('SERVICE_CLIENT_DRIVER', 'restful'),

    /*
    |--------------------------------------------------------------------------
    | Service Connections
    |--------------------------------------------------------------------------
    |
    | The default remote discovery of available services will be cached for efficiency
    | *.discover.driver client.php|client/config.php connections
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
                [
                    "ServiceID" => "",
                    "ServiceName" => "",
                    "ServiceAddress" => "",
                    "ServicePort" => ,
                ], // or  ServiceName
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service request client
    |--------------------------------------------------------------------------
    |
    | clients.*.driver client.php|client/config.php connections
    | clients.*.options client.php|client/config.php connections.*.settings
    |
    */

    'clients' => [
        'restful' => [
            'name' => 'restful',
            'driver' => 'http',
            'options' => [
                'timeout' => 1,
            ],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Service discovery refresh time(Minute)
    |--------------------------------------------------------------------------
    |
    | The default remote discovery of available services will be cached for efficiency
    |
    */

    'discover_refresh_time' => env('SERVICE_DISCOVER_REFRESH_TIME', 5),

    /*
    |--------------------------------------------------------------------------
    | Interactive authentication key
    |--------------------------------------------------------------------------
    |
    | The hash value used to generate data interaction between services
    |
    */
    
    'secret' => env('SERVICE_SECRET', null),

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