<?php
/**
 * Web routes for Gideons Technology application
 * 
 * This is the central route definition file for all web routes.
 * Routes are organized into logical groups and use controllers for handling requests.
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Controllers
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ServicesController;
use App\Controllers\GStoreController;
use App\Controllers\ContactController;
use App\Controllers\OrderController;
use App\Controllers\FintechController;
use App\Controllers\GTechController;
use App\Controllers\AdminController;

// Middleware
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\GuestMiddleware;

return function (App $app) {
    // ===================================================
    // PUBLIC ROUTES (No Authentication Required)
    // ===================================================
    
    // Homepage route
    $app->get('/', [HomeController::class, 'index'])->setName('home');
    $app->get('/index.php', [HomeController::class, 'index']);
    
    // Basic information pages
    $app->get('/about', [HomeController::class, 'about'])->setName('about');
    $app->get('/contact', [ContactController::class, 'index'])->setName('contact');
    $app->post('/contact', [ContactController::class, 'submit'])->setName('contact.submit');
    $app->get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->setName('privacy-policy');
    $app->get('/terms', [HomeController::class, 'terms'])->setName('terms');
    
    // Authentication routes - applying GuestMiddleware to prevent logged-in users from accessing
    $app->group('/auth', function (RouteCollectorProxy $group) {
        // Login routes
        $group->get('/login', [AuthController::class, 'loginForm'])->setName('auth.login');
        $group->post('/login', [AuthController::class, 'login'])->setName('auth.login.submit');
        
        // Registration routes
        $group->get('/register', [AuthController::class, 'registerForm'])->setName('auth.register');
        $group->post('/register', [AuthController::class, 'register'])->setName('auth.register.submit');
        
        // Password recovery routes
        $group->get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->setName('auth.forgot-password');
        $group->post('/forgot-password', [AuthController::class, 'forgotPassword'])->setName('auth.forgot-password.submit');
        $group->get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->setName('auth.reset-password');
        $group->post('/reset-password', [AuthController::class, 'resetPassword'])->setName('auth.reset-password.submit');
    })->add(new GuestMiddleware());
    
    // User-friendly auth URLs at root level (redirects to proper auth routes)
    $app->get('/login', function($request, $response) {
        return $response->withRedirect('/auth/login', 301);
    })->setName('login');
    $app->get('/register', function($request, $response) {
        return $response->withRedirect('/auth/register', 301);
    })->setName('register');
    $app->get('/forgot-password', function($request, $response) {
        return $response->withRedirect('/auth/forgot-password', 301);
    })->setName('forgot-password');
    
    // Logout route (no middleware, should be accessible to any logged-in user)
    $app->get('/auth/logout', [AuthController::class, 'logout'])->setName('auth.logout');

    // Services routes
    $app->group('/services', function (RouteCollectorProxy $group) {
        $group->get('', [ServicesController::class, 'index'])->setName('services');
        $group->get('/web-development', [ServicesController::class, 'webDevelopment'])->setName('services.web-development');
        
        // Web Development sub-services
        $group->get('/web-development/ecommerce', [ServicesController::class, 'ecommerce'])->setName('services.web-development.ecommerce');
        $group->get('/web-development/design', [ServicesController::class, 'design'])->setName('services.web-development.design');
        $group->get('/web-development/applications', [ServicesController::class, 'applications'])->setName('services.web-development.applications');
        
        // Other services
        $group->get('/fintech', [ServicesController::class, 'fintech'])->setName('services.fintech');
        $group->get('/repair', [ServicesController::class, 'repair'])->setName('services.repair');
    });

    // Fintech request endpoint
    $app->post('/fintech-request', [FintechController::class, 'processRequest'])->setName('fintech.request');

    // GStore routes
    $app->group('/gstore', function (RouteCollectorProxy $group) {
        // Main store pages
        $group->get('', [GStoreController::class, 'index'])->setName('gstore.index');
        $group->get('/', [GStoreController::class, 'index']);
        $group->get('/product/{id:[0-9]+}', [GStoreController::class, 'product'])->setName('gstore.product');
        $group->get('/category/{category}', [GStoreController::class, 'category'])->setName('gstore.category');
        
        // Cart routes
        $group->get('/cart', [GStoreController::class, 'cart'])->setName('gstore.cart');
        $group->post('/cart/add', [GStoreController::class, 'addToCart'])->setName('gstore.cart.add');
        $group->post('/cart/remove', [GStoreController::class, 'removeFromCart'])->setName('gstore.cart.remove');
        
        // Wishlist routes
        $group->get('/wishlist', [GStoreController::class, 'wishlist'])->setName('gstore.wishlist');
        $group->post('/wishlist/add', [GStoreController::class, 'addToWishlist'])->setName('gstore.wishlist.add');
        
        // Checkout routes
        $group->get('/checkout', [GStoreController::class, 'checkout'])->setName('gstore.checkout');
        $group->post('/checkout', [GStoreController::class, 'processCheckout'])->setName('gstore.process-checkout');
        
        // Order management
        $group->get('/orders', [GStoreController::class, 'orders'])->setName('gstore.orders');
        $group->get('/order/success/{id}', [GStoreController::class, 'orderSuccess'])->setName('gstore.order.success');
        
        // Coupons
        $group->get('/coupons', [GStoreController::class, 'coupons'])->setName('gstore.coupons');
    });

    // Order routes
    $app->get('/order', [OrderController::class, 'index'])->setName('order.index');
    $app->get('/order/{id}', [OrderController::class, 'show'])->setName('order.show');
    $app->post('/order', [OrderController::class, 'create'])->setName('order.create');
    
    // Legacy/redirect route - for compatibility - renamed to avoid conflicts
    $app->get('/order-redirect', function ($request, $response) {
        return $response->withHeader('Location', '/order')->withStatus(302);
    })->setName('orders.redirect');
    $app->get('/order-redirect/{id}', function ($request, $response, $args) {
        return $response->withHeader('Location', '/order/' . $args['id'])->withStatus(302);
    });

    // Contact routes
    $app->get('/contact', [ContactController::class, 'index'])->setName('contact');
    $app->post('/contact', [ContactController::class, 'submit']);

    // User dashboard routes - Protected by auth middleware
    $app->group('/dashboard', function (RouteCollectorProxy $group) {
        $group->get('', [HomeController::class, 'dashboard'])->setName('dashboard');
        $group->get('/profile', [HomeController::class, 'profile'])->setName('dashboard.profile');
        $group->post('/profile', [HomeController::class, 'updateProfile']);
    })->add(new AuthMiddleware($app->getContainer()));
};
