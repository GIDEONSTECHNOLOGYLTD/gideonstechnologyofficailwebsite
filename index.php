<?php
/**
 * Gideons Technology - Front Controller
 * 
 * Main entry point for the Gideons Technology application.
 * Handles environment setup, dependency injection, and routing.
 */

use App\Core\AppFactory;
use App\Utilities\Logger;
use App\Core\RouteLoader;
use App\Core\RouteRegistry;

// Define base directory for convenience
define('BASE_DIR', __DIR__);

// Load environment variables
require_once BASE_DIR . '/vendor/autoload.php';
if (file_exists(BASE_DIR . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
    $dotenv->load();
}

// Ensure DEBUG variable is set properly (handle both DEBUG and APP_DEBUG for compatibility)
if (!isset($_ENV['DEBUG']) && isset($_ENV['APP_DEBUG'])) {
    $_ENV['DEBUG'] = $_ENV['APP_DEBUG'];
} elseif (!isset($_ENV['DEBUG'])) {
    $_ENV['DEBUG'] = 'false'; // Default to false if not set
}

// Set up error reporting
ini_set('display_errors', $_ENV['DEBUG'] === 'true' ? 1 : 0);
error_reporting(E_ALL);

// Initialize logger
Logger::init(__DIR__ . '/logs/app.log', $_ENV['DEBUG'] === 'true');
Logger::info('Application starting');

// Make sure templates directories exist
$templateDirs = ['gstore', 'gtech', 'errors', 'admin', 'user'];
foreach ($templateDirs as $dir) {
    if (!is_dir(__DIR__ . "/templates/$dir")) {
        mkdir(__DIR__ . "/templates/$dir", 0755, true);
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Create app using our custom factory
$app = AppFactory::create();

// Get container
$container = $app->getContainer();

// Add maintenance middleware early in the stack
// This will check if the site is in maintenance mode and show appropriate page
$app->add($container->get('maintenanceMiddleware'));

// Define base routes
$app->get('/', function($request, $response) use ($container) {
    Logger::info('Homepage requested');
    return $container->get('renderer')->render($response, 'home.php', [
        'appName' => $container->get('settings')['appName'],
        'currentYear' => $container->get('settings')['currentYear'],
        'page' => 'home',
        'consoleLog' => method_exists('App\Utilities\Logger', 'getConsoleScript') ? Logger::getConsoleScript() : ''
    ]);
});

// Reset route registry to prevent any conflicts
RouteRegistry::clear();

// Define the list of route files to load in priority order
$routeFiles = [
    // Main routes first - these establish base routing patterns
    ['file' => 'routes/routes.php', 'flag' => 'MAIN_ROUTES_REGISTERED'],
    
    // Authentication routes - critical for login functionality
    ['file' => 'routes/auth.php', 'flag' => 'AUTH_ROUTES_REGISTERED'],
    
    // API routes
    ['file' => 'routes/api.php', 'flag' => 'API_ROUTES_REGISTERED'],
    
    // Feature-specific routes
    ['file' => 'routes/gstore.php', 'flag' => 'GSTORE_ROUTES_REGISTERED'],
    ['file' => 'routes/gtech.php', 'flag' => 'GTECH_ROUTES_REGISTERED'],
    
    // User & admin routes
    ['file' => 'routes/user.php', 'flag' => 'USER_ROUTES_REGISTERED'],
    ['file' => 'routes/admin.php', 'flag' => 'ADMIN_ROUTES_REGISTERED'],
];

// Load all route files using the improved RouteLoader
Logger::info('Loading routes using RouteLoader');
foreach ($routeFiles as $route) {
    RouteLoader::loadRoutes($app, $container, $route['file'], $route['flag']);
}

// Define a fallback route for when no other routes match
$app->any('{route:.*}', function ($request, $response) use ($container) {
    Logger::warning('404 Not Found: ' . $request->getUri()->getPath());
    return $container->get('renderer')->render($response->withStatus(404), 'errors/404.php', [
        'appName' => $container->get('settings')['appName'],
        'currentYear' => $container->get('settings')['currentYear'],
        'page' => '404',
        'requestedPath' => $request->getUri()->getPath()
    ]);
});

// Add error handling for priority queue errors
$app->getContainer()['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $logger = $container->get('logger');
        if (strpos($exception->getMessage(), 'no lowest priority node found') !== false) {
            $logger->error('Priority queue error: ' . $exception->getMessage());
            return $container->get('renderer')->render($response->withStatus(500), 'errors/500.php', [
                'appName' => $container->get('settings')['appName'],
                'currentYear' => $container->get('settings')['currentYear'],
                'page' => 'Error',
                'error' => 'An internal server error occurred'
            ]);
        }
        
        $logger->error('Uncaught Exception: ' . $exception->getMessage());
        return $container->get('renderer')->render($response->withStatus(500), 'errors/500.php', [
            'appName' => $container->get('settings')['appName'],
            'currentYear' => $container->get('settings')['currentYear'],
            'page' => 'Error',
            'error' => 'An internal server error occurred'
        ]);
    };
};

Logger::info('Routes loaded, running application');

// Run the application
$app->run();