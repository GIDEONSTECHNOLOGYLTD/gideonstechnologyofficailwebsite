<?php
/**
 * Dependency Injection Container Configuration
 * 
 * This file registers all application dependencies in the DI container
 */

use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Database\DatabaseManager;
use App\Database\Repository\UserRepository;
use App\Database\Repository\ProductRepository;
use App\Database\Repository\OrderRepository;
use App\Database\Repository\OrderItemRepository;
use App\Core\ConfigManager;

return function (ContainerBuilder $containerBuilder) {
    // Global settings
    $containerBuilder->addDefinitions([
        'settings' => [
            'appName' => $_ENV['APP_NAME'] ?? 'Gideons Technology Ltd',
            'currentYear' => date('Y'),
            'displayErrorDetails' => (bool)($_ENV['DEBUG'] ?? false),
            'logErrors' => true,
            'logErrorDetails' => true,
            'logger' => [
                'name' => 'app',
                'path' => __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'twig' => [
                'paths' => [
                    __DIR__ . '/../templates',
                ],
                'cache' => __DIR__ . '/../cache',
            ],
        ],
    ]);
    
    // Database connection
    $containerBuilder->addDefinition('db', function ($container) {
        return DatabaseManager::getInstance()->getConnection();
    });
    
    // Repositories
    $containerBuilder->addDefinition('userRepository', function ($container) {
        return new UserRepository();
    });
    
    $containerBuilder->addDefinition('productRepository', function ($container) {
        return new ProductRepository();
    });
    
    $containerBuilder->addDefinition('orderRepository', function ($container) {
        return new OrderRepository();
    });
    
    $containerBuilder->addDefinition('orderItemRepository', function ($container) {
        return new OrderItemRepository();
    });
    
    // Twig templates
    $containerBuilder->addDefinition('view', function ($container) {
        $settings = $container->get('settings');
        $view = Twig::create($settings['twig']['paths'], [
            'cache' => $settings['twig']['cache'],
            'auto_reload' => $settings['displayErrorDetails'],
        ]);
        
        // Add extensions if needed
        // $view->addExtension(new \Slim\Views\TwigExtension());
        
        return $view;
    });
    
    // Simple PHP renderer
    $containerBuilder->addDefinition('renderer', function ($container) {
        return new \App\View\PhpRenderer(__DIR__ . '/../templates/');
    });
    
    // Monolog logger
    $containerBuilder->addDefinition('logger', function ($container) {
        $settings = $container->get('settings');
        $logger = new Logger($settings['logger']['name']);
        $logger->pushHandler(new StreamHandler($settings['logger']['path'], $settings['logger']['level']));
        return $logger;
    });
    
    // Middleware
    $containerBuilder->addDefinition('maintenanceMiddleware', function ($container) {
        return new \App\Middleware\MaintenanceMiddleware(
            $container->get('renderer')
        );
    });
    
    // Mailer Service
    $containerBuilder->addDefinition('mailer', function ($container) {
        return new \App\Services\Mailer\GmailMailer();
    });
    
    // Controllers
    $containerBuilder->addDefinition('UserController', function ($container) {
        return new \App\Controller\UserController(
            $container->get('renderer'),
            $container->get('logger')
        );
    });
    
    $containerBuilder->addDefinition('PaymentController', function ($container) {
        return new \App\Controllers\PaymentController(
            $container->get('orderRepository'), 
            $container->get('php_renderer')
        );
    });
    
    // Admin Controllers
    $containerBuilder->addDefinition('App\\Controllers\\Admin\\ConfigController', function ($container) {
        return new \App\Controllers\Admin\ConfigController(
            $container->get('php_renderer')
        );
    });
    
    $containerBuilder->addDefinition('App\\Controllers\\Admin\\PaymentConfigController', function ($container) {
        return new \App\Controllers\Admin\PaymentConfigController(
            $container->get('php_renderer')
        );
    });
    
    $containerBuilder->addDefinition('App\\Controllers\\Admin\\PaymentController', function ($container) {
        return new \App\Controllers\Admin\PaymentController(
            $container->get('renderer')
        );
    });
    
    $containerBuilder->addDefinition('ProductController', function ($container) {
        return new \App\Controller\ProductController(
            $container->get('productRepository'),
            $container->get('renderer'),
            $container->get('logger')
        );
    });
    
    $containerBuilder->addDefinition('OrderController', function ($container) {
        return new \App\Controller\OrderController(
            $container->get('orderRepository'),
            $container->get('orderItemRepository'),
            $container->get('productRepository'),
            $container->get('renderer'),
            $container->get('logger')
        );
    });
    
    $containerBuilder->addDefinition('HomeController', function ($container) {
        return new \App\Controller\HomeController(
            $container->get('renderer'),
            $container->get('logger')
        );
    });
};
