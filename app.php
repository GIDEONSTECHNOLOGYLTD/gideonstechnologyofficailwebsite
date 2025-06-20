<?php
/**
 * Gideons Technology - Application Entry Point
 * 
 * This file serves as the main entry point for the application,
 * bootstrapping the Slim framework and handling basic initialization.
 */

// Define base path constant
define('BASE_PATH', __DIR__);

// Check PHP version requirement
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('This application requires PHP 7.4.0 or newer.');
}

// Load composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    die('Composer dependencies not installed. Run "composer install".');
}

// Include application bootstrap
$app = require_once __DIR__ . '/bootstrap.php';

// Register service providers
$providers = [
    \App\Providers\AppServiceProvider::class,
    \App\Providers\AuthServiceProvider::class,
    \App\Providers\EventServiceProvider::class,
    \App\Providers\RouteServiceProvider::class,
    \App\Providers\DatabaseServiceProvider::class,
];

// Register all service providers
$serviceContainer = \App\Core\ServiceContainer::getInstance();
foreach ($providers as $provider) {
    if (class_exists($provider)) {
        $serviceContainer->register(new $provider($app));
    }
}

// Initialize services that need to be booted before request handling
$serviceContainer->bootProviders();

// Handle CORS for API requests
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one you want to allow
    $allowed_origins = [
        'http://localhost:3000',
        'http://localhost:8080',
        'https://gideons-technology.com',
        'https://www.gideons-technology.com'
    ];
    
    if (in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    }
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// Get instance of Slim App
$app = \App\Core\Application::getInstance()->getApp();

return $app;
