<?php
/**
 * Error Logging Debug File
 * 
 * This file is used to debug Slim application errors
 */

// Display all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define base path constant
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Create a new Slim application
use Slim\Factory\AppFactory;

// Create App with default settings
$app = AppFactory::create();

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Register a simple test route
$app->get('/error-log', function ($request, $response) {
    $response->getBody()->write('<h1>Error Log Debug</h1>');
    return $response;
});

// Print debug information
echo "<h1>Slim Application Debug Information</h1>";

// Check if the Slim application is properly initialized
echo "<p>App variable is defined: " . (isset($app) ? 'Yes' : 'No') . "</p>";

// Check if routes are registered
$routeCollector = $app->getRouteCollector();
$routes = $routeCollector->getRoutes();
echo "<p>Number of routes registered: " . count($routes) . "</p>";

// Display route information
echo "<h2>Registered Routes:</h2>";
echo "<ul>";
foreach ($routes as $route) {
    echo "<li>";
    echo "Pattern: " . $route->getPattern() . ", ";
    echo "Methods: " . implode(", ", $route->getMethods());
    echo "</li>";
}
echo "</ul>";

// Check for common issues
echo "<h2>Common Issues Check:</h2>";
echo "<ul>";

// Check if the HomeController exists
echo "<li>HomeController exists: " . (class_exists('App\\Controllers\\HomeController') ? 'Yes' : 'No') . "</li>";

// Check if the AuthController exists
echo "<li>AuthController exists: " . (class_exists('App\\Controllers\\AuthController') ? 'Yes' : 'No') . "</li>";

// Check if the RouteRegistry class exists
echo "<li>RouteRegistry exists: " . (class_exists('App\\Core\\RouteRegistry') ? 'Yes' : 'No') . "</li>";

// Check if the SafeRouteLoader class exists
echo "<li>SafeRouteLoader exists: " . (class_exists('App\\Core\\SafeRouteLoader') ? 'Yes' : 'No') . "</li>";

echo "</ul>";

// Check for PHP errors
echo "<h2>PHP Error Log:</h2>";
$errorLog = error_get_last();
if ($errorLog) {
    echo "<pre>" . print_r($errorLog, true) . "</pre>";
} else {
    echo "<p>No PHP errors found.</p>";
}

// Run the application
try {
    $app->run();
} catch (\Exception $e) {
    echo "<h2>Error running application:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
