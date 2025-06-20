<?php
/**
 * App Factory Class
 * 
 * Creates and configures the Slim application with dependencies
 * Uses centralized dependency configuration from app/dependencies.php
 */

namespace App\Core;

use Slim\Factory\AppFactory as SlimAppFactory;
use DI\ContainerBuilder;
use DI\Container;
use App\Utilities\Logger;

class AppFactory
{
    /**
     * Create a new Slim application instance with dependency container
     *
     * @return \Slim\App
     */
    public static function create()
    {
        // Build DI Container with dependencies from dependencies.php
        $container = self::buildContainer();
        
        // Set container to create App with on AppFactory
        SlimAppFactory::setContainer($container);
        
        // Create and return app
        $app = SlimAppFactory::create();
        
        // Add error middleware if debug is enabled
        $displayErrorDetails = $_ENV['DEBUG'] === 'true';
        $app->addErrorMiddleware($displayErrorDetails, true, true);
        
        return $app;
    }
    
    /**
     * Build the DI container with all dependencies
     *
     * @return Container
     */
    private static function buildContainer(): Container
    {
        $containerBuilder = new ContainerBuilder();
        
        // Enable compilation for better performance in production
        if ($_ENV['DEBUG'] !== 'true') {
            $containerBuilder->enableCompilation(__DIR__ . '/../../cache/container');
            $containerBuilder->writeProxiesToFile(true, __DIR__ . '/../../cache/proxies');
        }
        
        // Load external dependencies file
        $dependencies = require_once __DIR__ . '/../dependencies.php';
        $dependencies($containerBuilder);
        
        try {
            // Build and return the container
            return $containerBuilder->build();
        } catch (\Exception $e) {
            Logger::error('Container build error: ' . $e->getMessage());
            throw $e;
        }
    }
}