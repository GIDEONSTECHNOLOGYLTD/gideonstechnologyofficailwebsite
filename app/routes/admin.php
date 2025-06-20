<?php
/**
 * Admin routes for Gideons Technology application
 * 
 * This file contains all admin-related routes, protected by AdminMiddleware.
 * Routes are organized by resource (users, products, orders, etc.)
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Controllers
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\OrderController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ServiceController;

// Middleware
use App\Middleware\AdminMiddleware;

return function (App $app) {
    // All admin routes are grouped under /admin and protected by AdminMiddleware
    $app->group('/admin', function (RouteCollectorProxy $group) {
        // Admin dashboard
        $group->get('', [DashboardController::class, 'index'])->setName('admin.dashboard');
        $group->get('/dashboard', [DashboardController::class, 'index'])->setName('admin.dashboard.alt');
        
        // User management
        $group->group('/users', function (RouteCollectorProxy $group) {
            $group->get('', [UserController::class, 'index'])->setName('admin.users');
            $group->get('/create', [UserController::class, 'create'])->setName('admin.users.create');
            $group->post('', [UserController::class, 'store'])->setName('admin.users.store');
            $group->get('/{id}', [UserController::class, 'show'])->setName('admin.users.show');
            $group->get('/{id}/edit', [UserController::class, 'edit'])->setName('admin.users.edit');
            $group->put('/{id}', [UserController::class, 'update'])->setName('admin.users.update');
            $group->delete('/{id}', [UserController::class, 'delete'])->setName('admin.users.delete');
        });
        
        // Product management
        $group->group('/products', function (RouteCollectorProxy $group) {
            $group->get('', [ProductController::class, 'index'])->setName('admin.products');
            $group->get('/create', [ProductController::class, 'create'])->setName('admin.products.create');
            $group->post('', [ProductController::class, 'store'])->setName('admin.products.store');
            $group->get('/{id}', [ProductController::class, 'show'])->setName('admin.products.show');
            $group->get('/{id}/edit', [ProductController::class, 'edit'])->setName('admin.products.edit');
            $group->put('/{id}', [ProductController::class, 'update'])->setName('admin.products.update');
            $group->delete('/{id}', [ProductController::class, 'delete'])->setName('admin.products.delete');
        });
        
        // Order management
        $group->group('/orders', function (RouteCollectorProxy $group) {
            $group->get('', [OrderController::class, 'index'])->setName('admin.orders');
            $group->get('/create', [OrderController::class, 'create'])->setName('admin.orders.create');
            $group->post('', [OrderController::class, 'store'])->setName('admin.orders.store');
            $group->get('/{id}', [OrderController::class, 'show'])->setName('admin.orders.show');
            $group->get('/{id}/edit', [OrderController::class, 'edit'])->setName('admin.orders.edit');
            $group->put('/{id}', [OrderController::class, 'update'])->setName('admin.orders.update');
            $group->delete('/{id}', [OrderController::class, 'delete'])->setName('admin.orders.delete');
            $group->put('/{id}/status', [OrderController::class, 'updateStatus'])->setName('admin.orders.status');
        });
        
        // Service management
        $group->group('/services', function (RouteCollectorProxy $group) {
            $group->get('', [ServiceController::class, 'index'])->setName('admin.services');
            $group->get('/create', [ServiceController::class, 'create'])->setName('admin.services.create');
            $group->post('', [ServiceController::class, 'store'])->setName('admin.services.store');
            $group->get('/{id}', [ServiceController::class, 'show'])->setName('admin.services.show');
            $group->get('/{id}/edit', [ServiceController::class, 'edit'])->setName('admin.services.edit');
            $group->put('/{id}', [ServiceController::class, 'update'])->setName('admin.services.update');
            $group->delete('/{id}', [ServiceController::class, 'delete'])->setName('admin.services.delete');
        });
        
    })->add(new AdminMiddleware());
};
