<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default connection name
    |--------------------------------------------------------------------------
    |
    */

    'connection' => env('SERVICE_CONNECTION', 'consul'),

    /*
    |--------------------------------------------------------------------------
    | Default client driver
    |--------------------------------------------------------------------------
    |
    */

    'client' => env('SERVICE_CLIENT', 'restful'),

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
            /*'ssl' => [
                'assistant' => 'v.csr'
            ],*/
            'services' => [],
        ],

        'local' => [
            'discover' => [
                [
                    "id" => "assistant_1",
                    "name" => "assistant",
                    "host" => "192.168.1.251",
                    "port" => 2222,
                ], // or  ServiceName
            ],
            'services' => [
                'assistant'
            ],
            /*'ssl' => [
                'assistant' => 'v.csr'
            ]*/
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
                //'verify' => resource_path('ssl/cacert.pem'),
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
    | Whether to enable microservice data encryption and decryption
    |--------------------------------------------------------------------------
    |
    */

    'secret_status' => env('SECRET_STATUS',true),

    /*
    |--------------------------------------------------------------------------
    | Microservice data encryption and decryption key
    |--------------------------------------------------------------------------
    |
    */

    'secret' => env('SECRET','#1#2@!##'),

    /*
    |--------------------------------------------------------------------------
    | Microservice data encryption and decryption method
    |--------------------------------------------------------------------------
    |
    */

    'secret_cipher' => env('SECRET_CIPHER','AES-256-CFB'),

    /*
    |--------------------------------------------------------------------------
    | Services selector
    |--------------------------------------------------------------------------
    |
    | Different selectors can be selected to select connections for multiple services in service discovery
    | RandSelector: Randomly select an available selector
    | RingSelector: A ring selector to ensure scheduling equalization
    | ResidentSelector: Always use the same available selector
    */

    'selector' => \CrCms\Microservice\Client\Selectors\RandSelector::class,

    /*
    |--------------------------------------------------------------------------
    | Services call events
    |--------------------------------------------------------------------------
    | The escalation event listener of the service execution result.
    | Must implement the CrCms\Microservice\Client\Contracts\CallEventContract interface
    */

    'events' => [

    ]
];