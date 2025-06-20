<?php
/**
 * Admin Payment Routes
 * 
 * Defines routes for admin payment and email configuration
 */

use App\Core\RouteRegistry;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Container\ContainerInterface;

// Prevent duplicate route registration
if (defined('ADMIN_PAYMENT_ROUTES_REGISTERED') && ADMIN_PAYMENT_ROUTES_REGISTERED === true) {
    return function() { /* Already loaded */ };
}

// Return a function that registers the routes
return function (App $app, ContainerInterface $container) {
    // Mark these routes as registered
    define('ADMIN_PAYMENT_ROUTES_REGISTERED', true);
    
    // Admin payment routes group
    $app->group('/admin', function (RouteCollectorProxy $group) {
        // Payment config routes
        if (!RouteRegistry::isRegistered('GET', '/admin/payment')) {
            RouteRegistry::register('GET', '/admin/payment');
            $group->get('/payment', 'App\\Controllers\\Admin\\PaymentConfigController:index');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/admin/payment/update')) {
            RouteRegistry::register('POST', '/admin/payment/update');
            $group->post('/payment/update', 'App\\Controllers\\Admin\\PaymentConfigController:update');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/admin/payment/test/{gateway}')) {
            RouteRegistry::register('POST', '/admin/payment/test/{gateway}');
            $group->post('/payment/test/{gateway}', 'App\\Controllers\\Admin\\PaymentConfigController:testGateway');
        }
        
        // Email config routes
        if (!RouteRegistry::isRegistered('GET', '/admin/email')) {
            RouteRegistry::register('GET', '/admin/email');
            $group->get('/email', 'App\\Controllers\\Admin\\PaymentConfigController:emailConfig');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/admin/email/update')) {
            RouteRegistry::register('POST', '/admin/email/update');
            $group->post('/email/update', 'App\\Controllers\\Admin\\PaymentConfigController:updateEmailConfig');
        }
        
        if (!RouteRegistry::isRegistered('POST', '/admin/email/test')) {
            RouteRegistry::register('POST', '/admin/email/test');
            $group->post('/email/test', 'App\\Controllers\\Admin\\PaymentConfigController:testEmail');
        }
    })->add('adminAuthMiddleware');
};
