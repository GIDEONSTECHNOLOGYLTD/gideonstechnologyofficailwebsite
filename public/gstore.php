<?php
/**
 * GStore Route Handler
 * 
 * This file handles direct requests to the /gstore route
 * and includes the appropriate GStore controller or template.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load the autoloader
require BASE_PATH . '/vendor/autoload.php';

// Import necessary classes
use DI\ContainerBuilder;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;

// Log the request
if (class_exists('\App\Utilities\Logger')) {
    \App\Utilities\Logger::info('Direct GStore request received');
}

// Check if we should use the controller or direct file
try {
    // First try to use the GStoreController if available
    if (class_exists('\App\Controllers\GStoreController')) {
        // Get the container configuration
        $containerConfig = require BASE_PATH . '/app/config/container.php';
        
        // Ensure containerConfig is an array
        if (!is_array($containerConfig)) {
            throw new \Exception('Container configuration must be an array');
        }
        
        // Create a proper DI container using PHP-DI
        $containerBuilder = new \DI\ContainerBuilder();
        
        // Add required dependencies that might be missing
        if (!isset($containerConfig['app'])) {
            $containerConfig['app'] = function() {
                return null;
            };
        }
        
        // Make sure settings are properly configured
        if (!isset($containerConfig['settings'])) {
            $containerConfig['settings'] = [
                'appName' => 'Gideon\'s Technology',
                'currentYear' => date('Y')
            ];
        }

        // Make sure renderer is properly configured
        if (!isset($containerConfig['renderer'])) {
            $containerConfig['renderer'] = function($c) {
                return new class($c) {
                    private $container;
                    
                    public function __construct($container) {
                        $this->container = $container;
                    }
                    
                    public function render($response, $template, $data) {
                        $templatePath = BASE_PATH . '/templates/' . $template;
                        
                        if (!file_exists($templatePath)) {
                            throw new \RuntimeException(sprintf('Template %s not found', $template));
                        }
                        
                        extract($data);
                        ob_start();
                        include $templatePath;
                        $output = ob_get_clean();
                        
                        $response->getBody()->write($output);
                        return $response;
                    }
                };
            };
        }
        
        $containerBuilder->addDefinitions($containerConfig);
        $container = $containerBuilder->build();
        
        // Add the database dependency if not already configured
        if (!isset($containerConfig['db'])) {
            $containerConfig['db'] = function() {
                // Initialize the database
                $database = new \App\Core\Database();
                return $database->getPdo();
            };
            
            // Rebuild the container with the added dependencies
            $containerBuilder = new \DI\ContainerBuilder();
            $containerBuilder->addDefinitions($containerConfig);
            $container = $containerBuilder->build();
        }
        
        // Initialize database manager singleton if needed
        if (!isset($containerConfig['databaseManager'])) {
            $containerConfig['databaseManager'] = function() {
                // Make sure DatabaseManager is initialized
                if (class_exists('\App\Core\DatabaseManager')) {
                    return \App\Core\DatabaseManager::getInstance();
                }
                return null;
            };
        }
        
        // Add the database dependency
        if (!isset($containerConfig['db'])) {
            $containerConfig['db'] = function() {
                // Initialize the database
                try {
                    if (class_exists('\App\Core\Database')) {
                        $database = new \App\Core\Database();
                        return $database->getPdo();
                    } elseif (class_exists('\App\Core\DatabaseManager')) {
                        return \App\Core\DatabaseManager::getConnection();
                    }
                    return null;
                } catch (\Exception $e) {
                    error_log('Failed to initialize database: ' . $e->getMessage());
                    return null;
                }
            };
        }
        
        // Rebuild the container with our added dependencies
        $containerBuilder = new \DI\ContainerBuilder();
        $containerBuilder->addDefinitions($containerConfig);
        $container = $containerBuilder->build();
        
        // Create the controller with the properly configured container
        $controller = new \App\Controllers\GStoreController($container);
        
        // Create a simple request and response object
        $request = new \Slim\Psr7\Request('GET', new \Slim\Psr7\Uri('http', 'localhost', null, '/gstore'), [], [], [], []);
        $response = new \Slim\Psr7\Response();
        
        // Call the controller method
        $response = $controller->index($request, $response);
        
        // Output the response body
        echo $response->getBody();
        exit;
    }
} catch (Exception $e) {
    // If controller approach fails, fall back to direct file inclusion
    if (file_exists(__DIR__ . '/gstore/index.php')) {
        include __DIR__ . '/gstore/index.php';
        exit;
    }
}

// If all else fails, use a simple redirect
header('Location: /gstore/');
exit;
