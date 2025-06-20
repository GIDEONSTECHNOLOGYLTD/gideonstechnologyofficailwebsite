<?php
/**
 * GTech Route Handler
 * 
 * This file handles direct requests to the /gtech route
 * and includes the appropriate GTech controller or template.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load the autoloader
require BASE_PATH . '/vendor/autoload.php';

// Log the request
if (class_exists('\App\Utilities\Logger')) {
    \App\Utilities\Logger::info('Direct GTech request received');
}

// Check if we should use the controller or direct file
try {
    // First try to use the GTechController if available
    if (class_exists('\App\Controllers\GTechController')) {
        // Create container using PHP-DI
        $containerBuilder = new \DI\ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        
        // Load container definitions
        $containerDefinitions = require BASE_PATH . '/app/config/container.php';
        $containerBuilder->addDefinitions($containerDefinitions);
        
        // Build the container
        $container = $containerBuilder->build();
        
        // Create controller with proper container
        $controller = new \App\Controllers\GTechController($container);
        
        // Create a proper request and response object
        $headers = new \Slim\Psr7\Headers();
        $cookies = [];
        $serverParams = [];
        $body = new \Slim\Psr7\Stream(fopen('php://temp', 'r+'));
        $request = new \Slim\Psr7\Request('GET', new \Slim\Psr7\Uri('http', 'localhost', null, '/gtech'), $headers, $cookies, $serverParams, $body);
        $response = new \Slim\Psr7\Response();
        
        // Call the controller method
        $response = $controller->index($request, $response);
        
        // Output the response body
        echo $response->getBody();
        exit;
    }
} catch (Exception $e) {
    // If controller approach fails, fall back to direct template inclusion
    if (file_exists(BASE_PATH . '/templates/gtech/index.php')) {
        // Set up template variables
        $appName = 'Gideon\'s Technology';
        $currentYear = date('Y');
        $page = 'gtech';
        
        // Sample services data
        $businessServices = [
            ['id' => 1, 'name' => 'IT Infrastructure Setup & Management', 'category' => 'business'],
            ['id' => 2, 'name' => 'Custom Software Development', 'category' => 'business'],
            ['id' => 3, 'name' => 'Cybersecurity Solutions', 'category' => 'business'],
            ['id' => 4, 'name' => 'Data Backup & Recovery', 'category' => 'business'],
            ['id' => 5, 'name' => 'Cloud Migration Services', 'category' => 'business']
        ];
        
        $individualServices = [
            ['id' => 6, 'name' => 'Computer Repair & Maintenance', 'category' => 'individual'],
            ['id' => 7, 'name' => 'Data Recovery', 'category' => 'individual'],
            ['id' => 8, 'name' => 'Smart Home Setup', 'category' => 'individual'],
            ['id' => 9, 'name' => 'Personal Tech Training', 'category' => 'individual'],
            ['id' => 10, 'name' => 'Device Optimization', 'category' => 'individual']
        ];
        
        // Include the template
        include BASE_PATH . '/templates/gtech/index.php';
        exit;
    }
}

// If all else fails, use a simple redirect
header('Location: /gtech/');
exit;
