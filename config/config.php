<?php

/**
 * Main Configuration File
 * Contains all application settings in one place
 */

return [
    // Application settings
    'app' => [
        'name' => 'Gideons Technology',
        'version' => '1.0.0',
        'env' => getenv('APP_ENV') ?: 'development',
        'debug' => (getenv('APP_DEBUG') === 'true') ?: true,
        'url' => getenv('APP_URL') ?: 'http://localhost:8000',
        'timezone' => 'UTC',
        'locale' => 'en'
    ],
    
    // Database settings
    'db' => [
        'driver' => getenv('DB_DRIVER') ?: 'mysql',
        'host' => getenv('DB_HOST') ?: 'localhost',
        'database' => getenv('DB_NAME') ?: 'gideons_tech',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => ''
    ],
    
    // Security settings
    'security' => [
        'password_min_length' => 8,
        'token_expiry' => 3600, // 1 hour
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutes
        'session_lifetime' => 1800, // 30 minutes
        'csrf_token_length' => 32
    ],
    
    // API settings
    'api' => [
        'version' => 'v1',
        'rate_limit' => 100,
        'rate_window' => 3600 // 1 hour
    ],
    
    // View settings
    'views' => [
        'path' => __DIR__ . '/../resources/views',
        'cache' => __DIR__ . '/../storage/cache/views',
        'templates' => [
            'path' => __DIR__ . '/../resources/templates'
        ]
    ],
    
    // Path settings
    'paths' => [
        'root' => dirname(__DIR__),
        'app' => dirname(__DIR__) . '/app',
        'public' => dirname(__DIR__) . '/public',
        'storage' => dirname(__DIR__) . '/storage',
        'uploads' => dirname(__DIR__) . '/public/uploads',
        'logs' => dirname(__DIR__) . '/storage/logs'
    ],
    
    // Providers to be registered
    'providers' => [
        'App\Providers\DatabaseServiceProvider',
        'App\Providers\RouteServiceProvider',
        'App\Providers\AppServiceProvider',
        'App\Providers\AuthServiceProvider'
    ]
];