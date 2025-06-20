<?php
/**
 * Gideons Technology - Application Bootstrap
 * 
 * This file initializes the Slim application and configures all middleware.
 */

// Define application path constants
if (!defined('BASE_PATH')) define('BASE_PATH', __DIR__);
if (!defined('APP_PATH')) define('APP_PATH', BASE_PATH . '/app');
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', BASE_PATH . '/config');
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', BASE_PATH . '/storage');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', BASE_PATH . '/public_html');
if (!defined('TEMPLATES_PATH')) define('TEMPLATES_PATH', BASE_PATH . '/templates');
if (!defined('VIEW_PATH')) define('VIEW_PATH', BASE_PATH . '/resources/views');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');

// Load composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load environment variables if .env file exists
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->safeLoad(); // Use safeLoad instead of load to prevent exceptions
}

// Start session with secure settings
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax'
    ]);
}

// Create required directories
$directories = [
    STORAGE_PATH,
    STORAGE_PATH . '/logs',
    STORAGE_PATH . '/cache',
    STORAGE_PATH . '/uploads',
    PUBLIC_PATH . '/uploads'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Load helper functions
require_once APP_PATH . '/helpers.php';

try {
    // Create Container using PHP-DI
    $container = new \DI\Container();

    // Set container to create App with
    \Slim\Factory\AppFactory::setContainer($container);

    // Create App
    $app = \Slim\Factory\AppFactory::create();

    // Add Routing Middleware
    $app->addRoutingMiddleware();

    // Add Error Middleware
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    // Set the base path if app is not in web root
    // $app->setBasePath('/your-base-path');

    // Register dependencies
    require CONFIG_PATH . '/dependencies.php';

    // Register middleware
    require CONFIG_PATH . '/middleware.php';

    // Register routes
    require CONFIG_PATH . '/routes.php';

    // Initialize the application
    $app = App\Core\Application::getInstance()->initialize();

    // Load routes using the consolidated route loader
    require_once __DIR__ . '/routes/index.php';
    $routeLoader = new \App\Core\RouteLoader($app);
    $routeLoader->loadRoutes();

    // Return app instance to index.php
    return $app;
} catch (\Throwable $e) {
    // Log the exception
    error_log('Bootstrap Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    // Display a user-friendly error if in production
    if (getenv('APP_ENV') !== 'development') {
        echo '<h1>Application Error</h1>';
        echo '<p>The application could not be started. Please check the logs for details.</p>';
    } else {
        // Show detailed error in development
        echo '<h1>Application Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    exit(1);
}