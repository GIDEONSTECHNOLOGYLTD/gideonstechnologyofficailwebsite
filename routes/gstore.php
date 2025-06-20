<?php
/**
 * GStore Routes
 * 
 * This file contains routes related to the online store functionality.
 * It handles product listings, categories, and shopping cart functionality.
 * 
 * @package     GideonsTechnology
 * @subpackage  Routes
 * @author      Gideon Aina
 * @version     1.0.0
 * @since       1.0.0
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\RouteRegistry;
use App\Controllers\GStoreController;

return function (App $app, $container = null) {
    // Get container if not provided
    if (!$container && method_exists($app, 'getContainer')) {
        $container = $app->getContainer();
    }
    
    // Register the main GStore route
    if (!RouteRegistry::isRegistered('GET', '/gstore')) {
        RouteRegistry::register('GET', '/gstore');
        $app->get('/gstore', GStoreController::class . ':index');
    }
    
    // Register GStore group routes
    $app->group('/gstore', function (RouteCollectorProxy $group) use ($container) {
        
        // Basic product routes
        if (!RouteRegistry::isRegistered('GET', '/gstore/products')) {
            RouteRegistry::register('GET', '/gstore/products');
            $group->get('/products', GStoreController::class . ':products');
        }
        
        // Category routes
        if (!RouteRegistry::isRegistered('GET', '/gstore/categories')) {
            RouteRegistry::register('GET', '/gstore/categories');
            $group->get('/categories', GStoreController::class . ':categories');
        }
        
        // Category with ID route
        if (!RouteRegistry::isRegistered('GET', '/gstore/categories/{id}')) {
            RouteRegistry::register('GET', '/gstore/categories/{id}');
            $group->get('/categories/{id}', GStoreController::class . ':categoryById');
        }
        
        // Register parameterized product route
        if (!RouteRegistry::isRegistered('GET', '/gstore/product/{id}')) {
            RouteRegistry::register('GET', '/gstore/product/{id}');
            $group->get('/product/{id}', GStoreController::class . ':productById');
        }
        
        // Cart routes
        if (!RouteRegistry::isRegistered('GET', '/gstore/cart')) {
            RouteRegistry::register('GET', '/gstore/cart');
            $group->get('/cart', GStoreController::class . ':cart');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/gstore/cart/add')) {
            RouteRegistry::register('POST', '/gstore/cart/add');
            $group->post('/cart/add', GStoreController::class . ':addToCart');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/gstore/cart/remove')) {
            RouteRegistry::register('POST', '/gstore/cart/remove');
            $group->post('/cart/remove', GStoreController::class . ':removeFromCart');
        }
        
        // Checkout routes
        if (!RouteRegistry::isRegistered('GET', '/gstore/checkout')) {
            RouteRegistry::register('GET', '/gstore/checkout');
            $group->get('/checkout', GStoreController::class . ':checkout');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/gstore/checkout/process')) {
            RouteRegistry::register('POST', '/gstore/checkout/process');
            $group->post('/checkout/process', GStoreController::class . ':processCheckout');
        }
    });
    
    // Admin store routes
    $app->group('/admin', function (RouteCollectorProxy $adminGroup) {
        // Product management - GStore specific admin routes
        $adminGroup->group('/gstore-products', function (RouteCollectorProxy $productGroup) {
            if (!RouteRegistry::isRegistered('GET', '/admin/gstore-products')) {
                RouteRegistry::register('GET', '/admin/gstore-products');
                $productGroup->get('', GStoreController::class . ':adminProducts');
            }
            
            if (!RouteRegistry::isRegistered('GET', '/admin/gstore-products/add')) {
                RouteRegistry::register('GET', '/admin/gstore-products/add');
                $productGroup->get('/add', GStoreController::class . ':addProductForm');
            }
            
            if (!RouteRegistry::isRegistered('POST', '/admin/gstore-products/add')) {
                RouteRegistry::register('POST', '/admin/gstore-products/add');
                $productGroup->post('/add', GStoreController::class . ':addProduct');
            }
            
            if (!RouteRegistry::isRegistered('GET', '/admin/gstore-products/edit/{id}')) {
                RouteRegistry::register('GET', '/admin/gstore-products/edit/{id}');
                $productGroup->get('/edit/{id}', GStoreController::class . ':editProductForm');
            }
            
            if (!RouteRegistry::isRegistered('POST', '/admin/gstore-products/edit/{id}')) {
                RouteRegistry::register('POST', '/admin/gstore-products/edit/{id}');
                $productGroup->post('/edit/{id}', GStoreController::class . ':editProduct');
            }
            
            if (!RouteRegistry::isRegistered('POST', '/admin/gstore-products/delete/{id}')) {
                RouteRegistry::register('POST', '/admin/gstore-products/delete/{id}');
                $productGroup->post('/delete/{id}', GStoreController::class . ':deleteProduct');
            }
        });
    });
};
