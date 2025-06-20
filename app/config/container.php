<?php
/**
 * Container Configuration
 * 
 * This file defines the dependency injection container configuration
 * for the application following Slim 4 best practices.
 */

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\GStoreController;
use App\Controllers\ServicesController;
use App\Controllers\AdminController;
use App\Controllers\DashboardController;
use App\Controllers\AuthController;
use App\Controllers\PaymentController;
use App\Controllers\Api\V1\AuthController as ApiAuthController;
use App\Controllers\Api\V1\UserController as ApiUserController;
use App\Controllers\Api\V1\ProductController;
use App\Controllers\Api\V1\OrderController as ApiOrderController;
use App\Controllers\Api\V1\ServiceController;
use App\Core\Database;
use App\Core\RouteRegistry;
use App\Core\RouteManager;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\CategoryRepository;
use App\Services\ValidationService;
use App\Services\ServiceManager;
use App\Utilities\Logger;

return [
    // Application settings
    'settings' => [
        'appName' => getenv('APP_NAME') ?: 'Gideon\'s Technology',
        'currentYear' => date('Y'),
        'displayErrorDetails' => (getenv('APP_ENV') === 'development'),
        'app' => [
            'name' => getenv('APP_NAME') ?: 'Gideon\'s Technology',
            'env' => getenv('APP_ENV') ?: 'production',
            'debug' => (getenv('APP_DEBUG') === 'true') ?: false,
            'url' => getenv('APP_URL') ?: 'http://localhost',
            'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
            'locale' => getenv('APP_LOCALE') ?: 'en',
        ],
        'db' => [
            'driver' => getenv('DB_DRIVER') ?: 'mysql',
            'host' => getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('DB_PORT') ?: '3306',
            'database' => getenv('DB_NAME') ?: 'gideons_tech',
            'username' => getenv('DB_USER') ?: 'root',
            'password' => getenv('DB_PASS') ?: '',
            'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
            'collation' => getenv('DB_COLLATION') ?: 'utf8mb4_unicode_ci',
            'prefix' => getenv('DB_PREFIX') ?: '',
        ],
    ],
    
    // Core services
    Database::class => function() {
        return new Database();
    },
    
    // Repositories
    UserRepository::class => function($c) {
        return new UserRepository($c->get(Database::class)->getPdo());
    },
    ProductRepository::class => function($c) {
        return new ProductRepository($c->get(Database::class)->getPdo());
    },
    OrderRepository::class => function($c) {
        return new OrderRepository($c->get(Database::class)->getPdo());
    },
    ServiceRepository::class => function($c) {
        return new ServiceRepository($c->get(Database::class)->getPdo());
    },
    CategoryRepository::class => function($c) {
        return new CategoryRepository($c->get(Database::class)->getPdo());
    },
    
    // Services
    ValidationService::class => function() {
        return new ValidationService();
    },
    ServiceManager::class => function($c) {
        return new ServiceManager($c);
    },
    
    // Controllers
    HomeController::class => function($c) {
        return new HomeController($c);
    },
    UserController::class => function($c) {
        return new UserController($c);
    },
    OrderController::class => function($c) {
        return new OrderController($c);
    },
    GStoreController::class => function($c) {
        return new GStoreController($c);
    },
    ServicesController::class => function($c) {
        return new ServicesController($c);
    },
    AdminController::class => function($c) {
        return new AdminController($c);
    },
    DashboardController::class => function($c) {
        return new DashboardController($c);
    },
    
    // Payment Controller for processing payments
    PaymentController::class => function($c) {
        return new PaymentController($c);
    },
    
    // Main AuthController
    AuthController::class => function($c) {
        return new AuthController($c);
    },
    
    // API Controllers
    ApiAuthController::class => function($c) {
        return new ApiAuthController($c->get(UserRepository::class));
    },
    ApiUserController::class => function($c) {
        return new ApiUserController($c->get(UserRepository::class));
    },
    ProductController::class => function($c) {
        return new ProductController($c->get(ProductRepository::class));
    },
    ApiOrderController::class => function($c) {
        return new ApiOrderController($c->get(OrderRepository::class));
    },
    ServiceController::class => function($c) {
        return new ServiceController($c->get(ServiceRepository::class));
    },
    
    // Renderer for templates
    'renderer' => function($c) {
        return new class($c) {
            private $container;
            
            public function __construct($container) {
                $this->container = $container;
            }
            
            public function render($response, $template, $data) {
                // Simple PHP template renderer
                $templatePath = dirname(dirname(__DIR__)) . '/templates/' . $template;
                
                if (!file_exists($templatePath)) {
                    throw new \RuntimeException(sprintf('Template %s not found', $template));
                }
                
                // Extract data to make it available in the template
                extract($data);
                
                // Capture the template output
                ob_start();
                include $templatePath;
                $output = ob_get_clean();
                
                $response->getBody()->write($output);
                return $response;
            }
        };
    },
    
    // Twig view renderer (if used)
    'twig.view' => function() {
        if (!class_exists('\\Twig\\Environment')) {
            return null;
        }
        
        $loader = new \Twig\Loader\FilesystemLoader(dirname(dirname(__DIR__)) . '/templates');
        $twig = new \Twig\Environment($loader, [
            'cache' => dirname(dirname(__DIR__)) . '/cache/twig',
            'debug' => (getenv('APP_ENV') === 'development'),
            'auto_reload' => true,
        ]);
        
        // Add extensions and globals
        if (class_exists('\\Twig\\Extension\\DebugExtension')) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        $twig->addGlobal('app_name', getenv('APP_NAME') ?: 'Gideon\'s Technology');
        $twig->addGlobal('asset_url', getenv('ASSET_URL') ?: '/assets');
        
        return $twig;
    },
    
    // Flash messages
    'flash' => function() {
        if (!class_exists('\\Slim\\Flash\\Messages')) {
            return null;
        }
        $storage = [];
        return new \Slim\Flash\Messages($storage);
    },
    
    // Route manager
    RouteManager::class => function($c) {
        $app = $c->get('app');
        return new RouteManager($app, $c);
    },
];
