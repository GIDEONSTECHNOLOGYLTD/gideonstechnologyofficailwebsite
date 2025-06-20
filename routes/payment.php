<?php
/**
 * Payment Routes
 * 
 * Defines routes for payment processing with PayPal and Stripe integration
 */

use App\Core\RouteRegistry;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Container\ContainerInterface;

// Return a function that registers the routes
// This follows the same pattern as other route files
return function (App $app, ContainerInterface $container) {
    // Prevent duplicate route registration
    if (defined('PAYMENT_ROUTES_REGISTERED') && PAYMENT_ROUTES_REGISTERED === true) {
        return;
    }
    
    // Mark these routes as registered
    define('PAYMENT_ROUTES_REGISTERED', true);
    
    // Payment routes group
    $app->group('/payment', function (RouteCollectorProxy $group) {
        // Show payment options for an order
        if (!RouteRegistry::isRegistered('GET', '/payment/{id}')) {
            RouteRegistry::register('GET', '/payment/{id}');
            $group->get('/{id}', 'App\\Controllers\\PaymentController:show');
        }
        
        // Process payment
        if (!RouteRegistry::isRegistered('POST', '/payment/process')) {
            RouteRegistry::register('POST', '/payment/process');
            $group->post('/process', 'App\\Controllers\\PaymentController:process');
        }
        
        // Payment callbacks
        if (!RouteRegistry::isRegistered('GET', '/payment/success')) {
            RouteRegistry::register('GET', '/payment/success');
            $group->get('/success', 'App\\Controllers\\PaymentController:success');
        }
        
        if (!RouteRegistry::isRegistered('GET', '/payment/cancel')) {
            RouteRegistry::register('GET', '/payment/cancel');
            $group->get('/cancel', 'App\\Controllers\\PaymentController:cancel');
        }
        
        // Payment gateway webhooks
        if (!RouteRegistry::isRegistered('POST', '/payment/webhook/{gateway}')) {
            RouteRegistry::register('POST', '/payment/webhook/{gateway}');
            $group->post('/webhook/{gateway}', 'App\\Controllers\\PaymentController:webhook');
        }
    });
};
