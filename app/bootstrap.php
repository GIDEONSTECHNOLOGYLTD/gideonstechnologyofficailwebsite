<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/storage/logs/php_errors.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define application path constants if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', BASE_PATH . '/config');
}

if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', BASE_PATH . '/public');
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', BASE_PATH . '/storage');
if (!defined('VIEW_PATH')) define('VIEW_PATH', BASE_PATH . '/resources/views');

// Define site URL for use in templates and redirects
if (!defined('SITE_URL')) {
    // Detect if we're using the PHP built-in server
    if (php_sapi_name() === 'cli-server') {
        define('SITE_URL', 'http://localhost:8000');
    } else {
        // For production, adjust this based on your domain
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        define('SITE_URL', $protocol . $domain);
    }
}

// Define site name
if (!defined('SITE_NAME')) {
    define('SITE_NAME', "Gideon's Technology");
}

// Load the composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load helpers
require_once __DIR__ . '/helpers.php';

// Load core classes
require_once __DIR__ . '/Core/View.php';
require_once __DIR__ . '/Core/Router.php';
require_once __DIR__ . '/Core/Route.php';
require_once __DIR__ . '/Core/Application.php';

// Load environment variables
if (class_exists('Dotenv\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->safeLoad();
}

// Create database directory if it doesn't exist
$dbDir = BASE_PATH . '/database';
if (!file_exists($dbDir)) {
    mkdir($dbDir, 0777, true);
}

// Initialize the application
$appInstance = App\Core\Application::getInstance();

// Get the Slim App instance from our Application
$app = $appInstance->initialize();

// Verify we have a valid Slim App instance
if (!($app instanceof \Slim\App)) {
    throw new \RuntimeException('Failed to initialize Slim App instance');
}

// Now load routes with the proper Slim App instance
$routes = require_once __DIR__ . '/routes/web.php';

// If routes file returns a callable, execute it with the app instance
if (is_callable($routes)) {
    $routes($app);
}

// Global functions
function url($path = '')
{
    global $app; // Make sure we have access to the global $app variable
    return $app->config('app.url') . $path;
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

function asset($path) {
    return url('/assets/' . $path);
}

function isActive($path) {
    return strpos($_SERVER['REQUEST_URI'], $path) === 0 ? 'active' : '';
}

// Return the application instance
return $app;
