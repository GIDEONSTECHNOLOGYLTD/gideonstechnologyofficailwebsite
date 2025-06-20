<?php

// Only define constants if they don't already exist
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(dirname(__DIR__)));
if (!defined('APP_PATH')) define('APP_PATH', dirname(__DIR__));
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', BASE_PATH . '/public');
if (!defined('UPLOADS_PATH')) define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Database configuration
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'database' => 'gideons_tech',
    'charset' => 'utf8mb4'
];

// Application configuration
$config = [
    'site_name' => 'Gideons Technology',
    'base_url' => 'http://localhost:8000',
    'timezone' => 'UTC',
    'debug' => true,
    'session_name' => 'gideons_tech_session',
    'session_lifetime' => 3600,
    'cookie_lifetime' => 86400,
    'csrf_token_name' => 'csrf_token',
    'password_min_length' => 8,
    'password_max_length' => 128
];

// Set timezone
date_default_timezone_set($config['timezone']);

// Error reporting
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session
session_name($config['session_name']);
if (session_status() == PHP_SESSION_NONE) {
    // Set session cookie parameters
    session_set_cookie_params(
        $config['cookie_lifetime'],
        '/',
        $_SERVER['HTTP_HOST'],
        true, // secure
        true  // httponly
    );
    session_start();
}
