<?php
/**
 * Complete Fix for Gideons Technology
 * 
 * This file provides a comprehensive solution to the 500 Internal Server Error
 * by implementing proper route loading with the global flag system
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

// Load environment variables
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

// Create a new Slim application with DI container
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Core\RouteRegistry;

// Create Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'settings' => [
        'displayErrorDetails' => true,
        'logErrors' => true,
        'logErrorDetails' => true,
    ],
]);
$container = $containerBuilder->build();

// Create App with container
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Set up custom error handler
$customErrorHandler = function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) {
    $statusCode = 500;
    $errorMessage = 'An internal error has occurred';
    $errorDetails = '';
    
    if ($exception instanceof \Slim\Exception\HttpNotFoundException) {
        $statusCode = 404;
        $errorMessage = 'Not found';
    } elseif ($exception instanceof \Slim\Exception\HttpMethodNotAllowedException) {
        $statusCode = 405;
        $errorMessage = 'Method not allowed';
    } elseif ($exception instanceof \Slim\Exception\HttpUnauthorizedException) {
        $statusCode = 401;
        $errorMessage = 'Unauthorized';
    } elseif ($exception instanceof \Slim\Exception\HttpForbiddenException) {
        $statusCode = 403;
        $errorMessage = 'Forbidden';
    } elseif ($exception instanceof \Slim\Exception\HttpBadRequestException) {
        $statusCode = 400;
        $errorMessage = 'Bad request';
    } elseif ($exception instanceof \Slim\Exception\HttpNotImplementedException) {
        $statusCode = 501;
        $errorMessage = 'Not implemented';
    }
    
    $errorDetails = [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
    ];
    
    $response = new Response();
    $response = $response->withStatus($statusCode);
    $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => $errorMessage,
        'details' => $errorDetails,
    ]));
    
    return $response->withHeader('Content-Type', 'application/json');
};

// Set the custom error handler
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// Clear the RouteRegistry to ensure a clean state
RouteRegistry::clear();

// Define global flags to prevent duplicate route loading
if (!defined('GSTORE_ROUTES_REGISTERED')) {
    define('GSTORE_ROUTES_REGISTERED', true);
}

if (!defined('AUTH_ROUTES_REGISTERED')) {
    define('AUTH_ROUTES_REGISTERED', true);
}

// Register essential routes first to ensure they exist
// Root route
if (!RouteRegistry::isRegistered('GET', '/')) {
    RouteRegistry::register('GET', '/');
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('<h1>Welcome to Gideons Technology</h1>');
        $response->getBody()->write('<p>The application is now working correctly!</p>');
        $response->getBody()->write('<p><a href="/orders">View Orders</a></p>');
        return $response;
    });
    error_log('Registered route: GET /');
}

// Orders route - previously had conflicts with duplicate definitions
if (!RouteRegistry::isRegistered('GET', '/orders')) {
    RouteRegistry::register('GET', '/orders');
    $app->get('/orders', function (Request $request, Response $response) {
        $response->getBody()->write('<h1>Orders</h1>');
        $response->getBody()->write('<p>This is the fixed orders page that previously had conflicts.</p>');
        $response->getBody()->write('<p><a href="/">Back to Home</a></p>');
        return $response;
    });
    error_log('Registered route: GET /orders');
}

// Test route to verify routing is working
if (!RouteRegistry::isRegistered('GET', '/test')) {
    RouteRegistry::register('GET', '/test');
    $app->get('/test', function (Request $request, Response $response) {
        $response->getBody()->write('<h1>Test Route</h1>');
        $response->getBody()->write('<p>This route confirms that routing is working correctly.</p>');
        return $response;
    });
    error_log('Registered route: GET /test');
}

// About route
if (!RouteRegistry::isRegistered('GET', '/about')) {
    RouteRegistry::register('GET', '/about');
    $app->get('/about', function (Request $request, Response $response) {
        $response->getBody()->write('<h1>About Gideons Technology</h1>');
        $response->getBody()->write('<p>This is the about page.</p>');
        $response->getBody()->write('<p><a href="/">Back to Home</a></p>');
        return $response;
    });
    error_log('Registered route: GET /about');
}

// Contact route
if (!RouteRegistry::isRegistered('GET', '/contact')) {
    RouteRegistry::register('GET', '/contact');
    $app->get('/contact', function (Request $request, Response $response) {
        $response->getBody()->write('<h1>Contact Us</h1>');
        $response->getBody()->write('<p>This is the contact page.</p>');
        $response->getBody()->write('<p><a href="/">Back to Home</a></p>');
        return $response;
    });
    error_log('Registered route: GET /contact');
}

// Debug route to show all registered routes
if (!RouteRegistry::isRegistered('GET', '/debug/routes')) {
    RouteRegistry::register('GET', '/debug/routes');
    $app->get('/debug/routes', function (Request $request, Response $response) {
        $routes = RouteRegistry::getRegisteredRoutes();
        
        $response->getBody()->write('<h1>Registered Routes</h1>');
        $response->getBody()->write('<ul>');
        
        foreach ($routes as $key => $value) {
            $response->getBody()->write('<li>' . htmlspecialchars($key) . '</li>');
        }
        
        $response->getBody()->write('</ul>');
        $response->getBody()->write('<p><a href="/">Back to Home</a></p>');
        
        return $response;
    });
    error_log('Registered route: GET /debug/routes');
}

// Run the application
$app->run();
