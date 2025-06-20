<?php

/**
 * Main Application Configuration
 * Consolidated from multiple config files
 */

// Ensure BASE_PATH is defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Ensure other path constants are defined
if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', BASE_PATH . '/config');
}

if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', BASE_PATH . '/storage');
}

return [
    'app' => [
        /*
        |--------------------------------------------------------------------------
        | Application Name
        |--------------------------------------------------------------------------
        |
        | This value is the name of your application. This value is used when the
        | framework needs to display the name of the application.
        |
        */
        'name' => getenv('APP_NAME') ?: 'Gideon\'s Technology',

        /*
        |--------------------------------------------------------------------------
        | Application Environment
        |--------------------------------------------------------------------------
        |
        | This determines the application environment. Different environments
        | allow you to customize behavior based on whether you're in development,
        | testing, or production.
        |
        */
        'env' => getenv('APP_ENV') ?: 'development',

        /*
        |--------------------------------------------------------------------------
        | Application Debug Mode
        |--------------------------------------------------------------------------
        |
        | When debug is enabled, detailed error messages with stack traces will be
        | shown for every error that occurs within your application.
        |
        */
        'debug' => (getenv('APP_DEBUG') === 'true') ?: true,
        
        /*
        |--------------------------------------------------------------------------
        | Application URL
        |--------------------------------------------------------------------------
        |
        | This URL is used by the console to properly generate URLs when using
        | the Artisan command line tool. You should set this to the root of
        | your application so that it's used when running Artisan tasks.
        |
        */
        'url' => getenv('APP_URL') ?: 'http://localhost',
        
        /*
        |--------------------------------------------------------------------------
        | Application Timezone
        |--------------------------------------------------------------------------
        |
        | Here you may specify the default timezone for your application, which
        | will be used by the PHP date and date-time functions.
        |
        */
        'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
        
        /*
        |--------------------------------------------------------------------------
        | Application Locale Configuration
        |--------------------------------------------------------------------------
        |
        | The application locale determines the default locale that will be used
        | by the translation service provider. You are free to set this value
        | to any of the locales which will be supported by the application.
        |
        */
        'locale' => getenv('APP_LOCALE') ?: 'en',
        
        /*
        |--------------------------------------------------------------------------
        | Application Fallback Locale
        |--------------------------------------------------------------------------
        |
        | The fallback locale determines the locale to use when the current one
        | is not available. You may change the value to correspond to any of
        | the language folders that are provided through your application.
        |
        */
        'fallback_locale' => 'en',
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on app startup.
    | Core providers are loaded automatically, so you can list your custom providers here.
    |
    */
    'providers' => [
        // App\Providers\AuthServiceProvider::class,
        // App\Providers\AppServiceProvider::class,
        // App\Providers\RouteServiceProvider::class,
        // Add more service providers here
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. These aliases provide convenience when working with classes.
    |
    */
    'aliases' => [
        'App' => App\Core\App::class,
        'Config' => App\Core\Config::class,
        'DB' => App\Core\DatabaseManager::class,
        'Route' => App\Core\Route::class,
        'Request' => App\Core\Request::class,
        'Response' => App\Core\Response::class,
        'Session' => App\Core\Session::class,
        'View' => App\Core\View::class,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the application. This is used for generating URLs.
    |
    */
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    
    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | The default timezone for your application.
    |
    */
    'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The default locale for your application.
    |
    */
    'locale' => getenv('APP_LOCALE') ?: 'en',

    'database' => [
        'driver' => 'mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'database' => $_ENV['DB_DATABASE'] ?? 'gideons_tech',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'path' => dirname(__DIR__) . '/database/database.sqlite',
    ],
    
    'security' => [
        'csrf_token_lifetime' => 7200, // 2 hours
        'session_lifetime' => 120, // 2 hours
        'password_hash_algorithm' => PASSWORD_BCRYPT,
        'password_hash_options' => [
            'cost' => 12
        ],
    ],
    
    'paths' => [
        'base' => dirname(__DIR__),
        'app' => dirname(__DIR__) . '/app',
        'public' => dirname(__DIR__) . '/public',
        'storage' => dirname(__DIR__) . '/storage',
        'resources' => dirname(__DIR__) . '/resources',
        'logs' => dirname(__DIR__) . '/storage/logs',
        'cache' => dirname(__DIR__) . '/storage/cache',
        'uploads' => dirname(__DIR__) . '/storage/uploads',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Migration
    |--------------------------------------------------------------------------
    |
    | Automatically run migrations on application start.
    |
    */
    'migrate_on_start' => (getenv('APP_AUTO_MIGRATE') === 'true') ?: false,
];
