<?php

return [
    'name' => 'Gideons Technology',
    'env' => 'development',
    'debug' => true,
    'url' => 'http://localhost:8000',
    'timezone' => 'UTC',
    'locale' => 'en',
    'key' => 'your-secret-key',
    'cipher' => 'AES-256-CBC',
    'log' => 'single',
    'providers' => [
        App\Core\Container::class,
        App\Core\Router::class,
    ],
    'aliases' => [
        'Container' => App\Core\Container::class,
        'Router' => App\Core\Router::class,
        'Response' => App\Core\Response::class,
    ],
];
