<?php
/**
 * Middleware Configuration
 * 
 * This file defines the middleware stack for the application.
 * Middleware is executed in the order it is added to the stack.
 */

use Slim\App;
use Slim\Middleware\MethodOverrideMiddleware;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\ApiRateLimitMiddleware;
use App\Middleware\CorsMiddleware;
use App\Core\ErrorHandler;

return function (App $app) {
    // IMPORTANT: The order of middleware matters!
    
    // Add routing middleware first - this is required for routing to work
    $app->addRoutingMiddleware();
    
    // Add method override middleware - allows PUT/DELETE methods to be simulated in browsers
    $app->add(new MethodOverrideMiddleware());
    
    // Add body parsing middleware - parses request body according to content type
    $app->addBodyParsingMiddleware();
    
    // Add compression middleware for better performance
    if (class_exists('\\App\\Middleware\\CompressionMiddleware')) {
        $compressionConfig = [
            'enabled' => true,
            'min_length' => 1024,  // Only compress responses larger than 1KB
            'level' => 6           // Compression level (1-9, where 9 is maximum compression)
        ];
        $app->add(new \App\Middleware\CompressionMiddleware($compressionConfig));
    }
    
    // Add response caching middleware for better performance
    if (class_exists('\\App\\Middleware\\CacheMiddleware')) {
        $cacheConfig = [
            'enabled' => getenv('APP_ENV') !== 'development',  // Disable in development
            'routes' => [
                '/api/v1/products' => 600,           // Cache product list for 10 minutes
                '/api/v1/categories' => 1800,        // Cache categories for 30 minutes
                '/gstore/featured' => 300            // Cache featured products for 5 minutes
            ],
            'default_lifetime' => 60  // Default cache lifetime of 1 minute
        ];
        $app->add(new \App\Middleware\CacheMiddleware($cacheConfig));
    }
    
    // Add CORS middleware if needed
    if (class_exists('\\App\\Middleware\\CorsMiddleware')) {
        $app->add(new \App\Middleware\CorsMiddleware());
    }
    
    // Add error middleware last - this ensures all other middleware is processed first
    $errorMiddleware = $app->addErrorMiddleware(
        getenv('APP_ENV') === 'development', // Display detailed errors in development
        true,                               // Log errors
        true                                // Display error details in error log
    );
    
    // Set custom error handler if available
    if (class_exists('\\App\\Core\\ErrorHandler')) {
        $errorHandler = ErrorHandler::getInstance();
        $errorMiddleware->setDefaultErrorHandler([$errorHandler, 'handleException']);
    }
    
    return $app;
};
