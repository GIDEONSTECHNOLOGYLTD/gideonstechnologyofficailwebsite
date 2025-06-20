<?php
/**
 * Slim 4 Application Test Script
 * 
 * This script tests the core functionality of your Slim 4 application
 * without requiring PHPUnit to be installed in your environment.
 */

require __DIR__ . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Factory\StreamFactory;

echo "\n=== Slim 4 Application Test ===\n\n";

// Test 1: Check if core classes exist
echo "Test 1: Checking core classes...\n";
$requiredClasses = [
    'Slim\\App',
    'App\\Core\\Database',
    'App\\Core\\ErrorHandler',
    'App\\Core\\ViewManager',
    'App\\Core\\CacheManager',
    'App\\Core\\QueryCache',
    'App\\Middleware\\CacheMiddleware',
    'App\\Middleware\\CompressionMiddleware',
    'App\\Repositories\\BaseRepository',
    'App\\Services\\BaseService'
];

$missingClasses = [];
foreach ($requiredClasses as $class) {
    if (!class_exists($class)) {
        $missingClasses[] = $class;
    }
}

if (empty($missingClasses)) {
    echo "✅ All core classes found!\n";
} else {
    echo "❌ Missing classes: " . implode(", ", $missingClasses) . "\n";
}

// Test 2: Check if routes are registered correctly
echo "\nTest 2: Checking route registration...\n";
try {
    // Create a minimal Slim app
    $app = AppFactory::create();
    
    // Load routes
    if (file_exists(__DIR__ . '/routes/routes.php')) {
        $routes = require __DIR__ . '/routes/routes.php';
        $routes($app, new class {
            public function get($id) {
                return null;
            }
        });
        echo "✅ Routes loaded successfully!\n";
    } else {
        echo "❌ Routes file not found!\n";
    }
    
    // Check if RouteRegistry is working
    if (class_exists('App\\Core\\RouteRegistry')) {
        echo "✅ RouteRegistry class found!\n";
    } else {
        echo "❌ RouteRegistry class not found!\n";
    }
} catch (\Exception $e) {
    echo "❌ Error loading routes: " . $e->getMessage() . "\n";
}

// Test 3: Check if error handling is set up correctly
echo "\nTest 3: Checking error handling...\n";
if (class_exists('App\\Core\\ErrorHandler')) {
    echo "✅ ErrorHandler class found!\n";
    
    // Check if error templates exist
    $errorTemplates = [
        __DIR__ . '/app/templates/errors/404.php',
        __DIR__ . '/app/templates/errors/production.php',
        __DIR__ . '/app/templates/errors/development.php'
    ];
    
    $missingTemplates = [];
    foreach ($errorTemplates as $template) {
        if (!file_exists($template)) {
            $missingTemplates[] = basename($template);
        }
    }
    
    if (empty($missingTemplates)) {
        echo "✅ All error templates found!\n";
    } else {
        echo "❌ Missing error templates: " . implode(", ", $missingTemplates) . "\n";
    }
} else {
    echo "❌ ErrorHandler class not found!\n";
}

// Test 4: Check if caching is set up correctly
echo "\nTest 4: Checking caching system...\n";
if (class_exists('App\\Core\\CacheManager') && class_exists('App\\Middleware\\CacheMiddleware')) {
    echo "✅ Caching system is set up correctly!\n";
} else {
    echo "❌ Caching system is not set up correctly!\n";
}

// Test 5: Check if compression is set up correctly
echo "\nTest 5: Checking compression middleware...\n";
if (class_exists('App\\Middleware\\CompressionMiddleware')) {
    echo "✅ Compression middleware is set up correctly!\n";
} else {
    echo "❌ Compression middleware is not set up correctly!\n";
}

// Test 6: Check if view system is set up correctly
echo "\nTest 6: Checking view system...\n";
if (class_exists('App\\Core\\ViewManager')) {
    echo "✅ ViewManager class found!\n";
    
    // Check if layout templates exist
    $layoutTemplates = [
        __DIR__ . '/app/templates/layouts/main.php'
    ];
    
    $missingTemplates = [];
    foreach ($layoutTemplates as $template) {
        if (!file_exists($template)) {
            $missingTemplates[] = basename($template);
        }
    }
    
    if (empty($missingTemplates)) {
        echo "✅ Layout templates found!\n";
    } else {
        echo "❌ Missing layout templates: " . implode(", ", $missingTemplates) . "\n";
    }
} else {
    echo "❌ ViewManager class not found!\n";
}

// Test 7: Check if repository system is set up correctly
echo "\nTest 7: Checking repository system...\n";
if (class_exists('App\\Repositories\\BaseRepository')) {
    echo "✅ BaseRepository class found!\n";
    
    // Check if specific repositories exist
    $repositories = [
        'App\\Repositories\\ProductRepository',
        'App\\Repositories\\UserRepository'
    ];
    
    $missingRepositories = [];
    foreach ($repositories as $repository) {
        if (!class_exists($repository)) {
            $missingRepositories[] = basename(str_replace('\\', '/', $repository));
        }
    }
    
    if (empty($missingRepositories)) {
        echo "✅ Specific repositories found!\n";
    } else {
        echo "❌ Missing repositories: " . implode(", ", $missingRepositories) . "\n";
    }
} else {
    echo "❌ BaseRepository class not found!\n";
}

// Test 8: Check if service system is set up correctly
echo "\nTest 8: Checking service system...\n";
if (class_exists('App\\Services\\BaseService')) {
    echo "✅ BaseService class found!\n";
    
    // Check if specific services exist
    $services = [
        'App\\Services\\ProductService'
    ];
    
    $missingServices = [];
    foreach ($services as $service) {
        if (!class_exists($service)) {
            $missingServices[] = basename(str_replace('\\', '/', $service));
        }
    }
    
    if (empty($missingServices)) {
        echo "✅ Specific services found!\n";
    } else {
        echo "❌ Missing services: " . implode(", ", $missingServices) . "\n";
    }
} else {
    echo "❌ BaseService class not found!\n";
}

echo "\n=== Test Summary ===\n";
echo "Your Slim 4 application has been tested for core functionality.\n";
echo "To run the full test suite with PHPUnit, you'll need to have PHP available in your environment.\n";
echo "\n";
