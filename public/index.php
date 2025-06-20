<?php
/**
 * Gideon's Technology Application Entry Point
 * 
 * This file serves as the entry point for all requests with proper route loading
 * and middleware configuration following Slim 4 best practices.
 */

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use App\Core\RouteRegistry;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Set the base directory
define('BASE_DIR', dirname(__DIR__));

// Load the autoloader first to ensure all classes are available
require BASE_DIR . '/vendor/autoload.php';

// Initialize RouteRegistry if it exists
if (class_exists('\App\Core\RouteRegistry')) {
    // Clear any existing registrations to prevent conflicts
    \App\Core\RouteRegistry::clear();
}

// Load environment variables
if (file_exists(BASE_DIR . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_DIR);
    $dotenv->load();
}

// Initialize error handling and logging
if (class_exists('\App\Utilities\Logger')) {
    \App\Utilities\Logger::init();
}

// NOTE: We're removing direct route handling that bypasses the RouteRegistry
// All routes should be registered through the proper route files to prevent duplicates
// The Slim application will handle all routing through the routes.php file

// Create the container
try {
    // Create Container using PHP-DI
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->useAutowiring(true);
    
    // Add container definitions
    $definitions = require BASE_DIR . '/app/config/container.php';
    $containerBuilder->addDefinitions($definitions);
    
    // Build the container
    $container = $containerBuilder->build();
    
    // Create the app with the container
    AppFactory::setContainer($container);
    $app = AppFactory::create();
    
    // Add routing middleware first
    $app->addRoutingMiddleware();
    
    // Add body parsing middleware
    $app->addBodyParsingMiddleware();
    
    // Add error middleware last
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    
    // Register all routes
    $routes = require BASE_DIR . '/routes/routes.php';
    $routes($app, $container);
    
    // Run the application
    $app->run();
    
} catch (\Throwable $e) {
    // Handle fatal errors
    if (class_exists('\App\Core\ErrorHandler')) {
        $errorHandler = \App\Core\ErrorHandler::getInstance();
        $errorHandler->handleException($e);
    } else {
        // Fallback error handling if ErrorHandler is not available
        header('HTTP/1.1 500 Internal Server Error');
        if (getenv('APP_ENV') === 'development') {
            echo '<h1>Application Error</h1>';
            echo '<p>' . $e->getMessage() . '</p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        } else {
            echo '<h1>Internal Server Error</h1>';
            echo '<p>An unexpected error occurred. Please try again later.</p>';
        }
    }
}
