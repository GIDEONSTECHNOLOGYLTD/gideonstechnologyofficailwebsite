<?php
/**
 * Routes Check Debug File
 * 
 * This file helps debug route registration issues
 */

// Define base path constant
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Set error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Create a new Slim application
use Slim\Factory\AppFactory;
use App\Core\RouteRegistry;

// Create App
$app = AppFactory::create();

// Register test routes
$app->get('/test-route', function ($request, $response) {
    $response->getBody()->write('Test route works!');
    return $response;
});

// Register the orders route
$app->get('/orders', function ($request, $response) {
    $response->getBody()->write('<h1>Orders Debug</h1><p>This is a test orders route.</p>');
    return $response;
});

// HTML header
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routes Check</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1, h2 { color: #333; }
        .card { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .warning { background: #fff3cd; color: #856404; }
        .error { background: #f8d7da; color: #721c24; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Routes Check Debug</h1>
';

// Check if the RouteRegistry class exists
echo '<div class="card ' . (class_exists('App\\Core\\RouteRegistry') ? 'success' : 'error') . '">';
echo '<h2>RouteRegistry Class</h2>';
echo '<p>' . (class_exists('App\\Core\\RouteRegistry') ? 'Found' : 'Not Found') . '</p>';
echo '</div>';

// Get registered routes from the app
$routes = $app->getRouteCollector()->getRoutes();
echo '<div class="card">';
echo '<h2>Registered Routes in This File</h2>';
echo '<p>Number of routes: ' . count($routes) . '</p>';
echo '<table>';
echo '<tr><th>Method</th><th>Pattern</th><th>Name</th></tr>';
foreach ($routes as $route) {
    echo '<tr>';
    echo '<td>' . implode(', ', $route->getMethods()) . '</td>';
    echo '<td>' . $route->getPattern() . '</td>';
    echo '<td>' . ($route->getName() ?: 'unnamed') . '</td>';
    echo '</tr>';
}
echo '</table>';
echo '</div>';

// Check RouteRegistry for registered routes
if (class_exists('App\\Core\\RouteRegistry')) {
    echo '<div class="card">';
    echo '<h2>Routes in RouteRegistry</h2>';
    
    // Check if the getRegisteredRoutes method exists
    if (method_exists('App\\Core\\RouteRegistry', 'getRegisteredRoutes')) {
        $registeredRoutes = RouteRegistry::getRegisteredRoutes();
        echo '<p>Number of routes in registry: ' . count($registeredRoutes) . '</p>';
        echo '<table>';
        echo '<tr><th>Method|Pattern</th></tr>';
        foreach ($registeredRoutes as $key => $value) {
            echo '<tr><td>' . $key . '</td></tr>';
        }
        echo '</table>';
    } else {
        // If the method doesn't exist, we'll check if specific routes are registered
        echo '<p>getRegisteredRoutes method not found. Checking specific routes:</p>';
        echo '<table>';
        echo '<tr><th>Method</th><th>Pattern</th><th>Registered</th></tr>';
        
        // Check common routes
        $routesToCheck = [
            ['GET', '/'],
            ['GET', '/orders'],
            ['GET', '/about'],
            ['GET', '/contact'],
            ['GET', '/test']
        ];
        
        foreach ($routesToCheck as $route) {
            $method = $route[0];
            $pattern = $route[1];
            $isRegistered = RouteRegistry::isRegistered($method, $pattern);
            
            echo '<tr>';
            echo '<td>' . $method . '</td>';
            echo '<td>' . $pattern . '</td>';
            echo '<td class="' . ($isRegistered ? 'success' : 'error') . '">' . ($isRegistered ? 'Yes' : 'No') . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
    
    echo '</div>';
}

// Check for route conflicts
echo '<div class="card">';
echo '<h2>Test Route Registration</h2>';

// Try to register the root route
try {
    $app->get('/', function ($request, $response) {
        return $response;
    });
    echo '<p class="success">Root route registered successfully.</p>';
} catch (\Exception $e) {
    echo '<p class="error">Error registering root route: ' . $e->getMessage() . '</p>';
}

// Try to register the orders route again
try {
    $app->get('/orders', function ($request, $response) {
        return $response;
    });
    echo '<p class="success">Orders route registered successfully.</p>';
} catch (\Exception $e) {
    echo '<p class="error">Error registering orders route: ' . $e->getMessage() . '</p>';
}

echo '</div>';

// HTML footer
echo '</div>
</body>
</html>';
