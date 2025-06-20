<?php

use DI\Container;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;
use Slim\Views\PhpRenderer;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Controllers\StoreController;
use App\Controllers\TestController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ServerRequestFactory;
use Twig\Environment as TwigEnvironment;

return [
    // Request
    'request' => function () {
        return (new ServerRequestFactory())->createFromGlobals();
    },
    
    // Settings
    'settings' => function () {
        return require __DIR__ . '/settings.php';
    },
    
    // Twig Loader
    'twig.loader' => function (PsrContainerInterface $c) {
        $settings = $c->get('settings')['view'];
        return new TwigFilesystemLoader($settings['template_path']);
    },
    
    // Bind the FilesystemLoader to the LoaderInterface
    TwigLoaderInterface::class => \DI\get('twig.loader'),
    
    // Twig View
    'twig.view' => function (PsrContainerInterface $c) {
        $loader = $c->get(TwigLoaderInterface::class);
        $settings = $c->get('settings')['view'];
        
        // Create cache directory if it doesn't exist
        if (!file_exists($settings['cache_path'])) {
            mkdir($settings['cache_path'], 0777, true);
        }
        
        // Create Twig environment with debug enabled
        $twig = new Twig($loader, [
            'cache' => false, // Disable cache for development
            'debug' => true,
            'auto_reload' => true,
        ]);
        
        // Add debug extension if debug is enabled
        if ($settings['debug']) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        
        return $twig;
    },
    
    // Alias for Twig View
    Twig::class => function (PsrContainerInterface $c) {
        return $c->get('twig.view');
    },
    
    // PHP View Renderer
    PhpRenderer::class => function (PsrContainerInterface $c) {
        $settings = $c->get('settings')['view'];
        return new PhpRenderer($settings['template_path']);
    },
    
    // Flash messages
    Messages::class => function () {
        return new Messages();
    },
    
    // Store Controller
    StoreController::class => function (PsrContainerInterface $c) {
        return new StoreController(
            $c->get('twig.view'),
            $c->get(Messages::class),
            $c->get(PhpRenderer::class),
            new Product(),
            new Category(),
            new Cart(),
            new Order()
        );
    },
    
    // Test Controller
    TestController::class => function (PsrContainerInterface $c) {
        return new TestController(
            $c->get('twig.view'),
            $c->get(Messages::class)
        );
    },
    
    // Twig Test Controller (no database dependency)
    'App\\Controllers\\TwigTestController' => function (PsrContainerInterface $c) {
        return new \App\Controllers\TwigTestController(
            $c->get('twig.view')
        );
    },
    
    // Models
    Product::class => function () {
        return new Product();
    },
    
    Category::class => function () {
        return new Category();
    },
    
    Cart::class => function () {
        return new Cart();
    },
    
    User::class => function () {
        return new User();
    },
    
    Order::class => function () {
        return new Order();
    },
    
    // Admin Controllers
    'App\\Controllers\\Admin\\DashboardController' => function (PsrContainerInterface $c) {
        return new \App\Controllers\Admin\DashboardController($c);
    },
    
    'App\\Controllers\\Admin\\SettingsController' => function (PsrContainerInterface $c) {
        return new \App\Controllers\Admin\SettingsController($c);
    }
];
