<?php
/**
 * User Routes
 * 
 * This file contains all user routes (dashboard, profile, settings, etc.)
 * Protected by AuthMiddleware
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UserController;
use App\Core\RouteRegistry;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utilities\Logger;
use App\Middleware\AuthMiddleware;

// Define a global flag to prevent duplicate loading
if (!defined('USER_ROUTES_REGISTERED')) {
    define('USER_ROUTES_REGISTERED', true);
    
    return function (App $app, ContainerInterface $container) {
        // Log that we're processing user routes
        if (class_exists('\App\Utilities\Logger')) {
            Logger::info("Processing user routes");
        }
        
        // Group all user routes under /user prefix and protect with AuthMiddleware
        $app->group('/user', function (RouteCollectorProxy $group) {
            // Dashboard
            if (!RouteRegistry::isRegistered('GET', '/user/dashboard')) {
                RouteRegistry::register('GET', '/user/dashboard');
                $group->get('/dashboard', [\App\Controllers\UserController::class, 'dashboard'])
                      ->setName('user.dashboard');
            }
            
            // Profile
            if (!RouteRegistry::isRegistered('GET', '/user/profile')) {
                RouteRegistry::register('GET', '/user/profile');
                $group->get('/profile', [\App\Controllers\UserController::class, 'profile'])
                      ->setName('user.profile');
            }
            
            // Update Profile
            if (!RouteRegistry::isRegistered('POST', '/user/profile/update')) {
                RouteRegistry::register('POST', '/user/profile/update');
                $group->post('/profile/update', [\App\Controllers\UserController::class, 'updateProfile'])
                      ->setName('user.profile.update');
            }
            
            // Service Requests - List all user service requests
            if (!RouteRegistry::isRegistered('GET', '/user/service-requests')) {
                RouteRegistry::register('GET', '/user/service-requests');
                $group->get('/service-requests', [\App\Controllers\UserController::class, 'serviceRequests'])
                      ->setName('user.service-requests');
            }
            
            // Service Request Detail
            if (!RouteRegistry::isRegistered('GET', '/user/service-request/{id}')) {
                RouteRegistry::register('GET', '/user/service-request/{id}');
                $group->get('/service-request/{id}', [\App\Controllers\UserController::class, 'serviceRequestDetail'])
                      ->setName('user.service-request.detail');
            }
            
            // New Service Request Form
            if (!RouteRegistry::isRegistered('GET', '/user/new-service-request')) {
                RouteRegistry::register('GET', '/user/new-service-request');
                $group->get('/new-service-request', [\App\Controllers\UserController::class, 'newServiceRequestForm'])
                      ->setName('user.new-service-request');
            }
            
            // Create Service Request
            if (!RouteRegistry::isRegistered('POST', '/user/service-request/create')) {
                RouteRegistry::register('POST', '/user/service-request/create');
                $group->post('/service-request/create', [\App\Controllers\UserController::class, 'createServiceRequest'])
                      ->setName('user.service-request.create');
            }
            
            // Cancel Service Request
            if (!RouteRegistry::isRegistered('POST', '/user/service-request/{id}/cancel')) {
                RouteRegistry::register('POST', '/user/service-request/{id}/cancel');
                $group->post('/service-request/{id}/cancel', [\App\Controllers\UserController::class, 'cancelServiceRequest'])
                      ->setName('user.service-request.cancel');
            }
            
            // Service History
            if (!RouteRegistry::isRegistered('GET', '/user/service-history')) {
                RouteRegistry::register('GET', '/user/service-history');
                $group->get('/service-history', [\App\Controllers\UserController::class, 'serviceHistory'])
                      ->setName('user.service-history');
            }
            
            // Add Feedback to Service History
            if (!RouteRegistry::isRegistered('POST', '/user/service-history/{id}/feedback')) {
                RouteRegistry::register('POST', '/user/service-history/{id}/feedback');
                $group->post('/service-history/{id}/feedback', [\App\Controllers\UserController::class, 'addServiceFeedback'])
                      ->setName('user.service-history.feedback');
            }
            
            // Consultations
            if (!RouteRegistry::isRegistered('GET', '/user/consultations')) {
                RouteRegistry::register('GET', '/user/consultations');
                $group->get('/consultations', [\App\Controllers\UserController::class, 'consultations'])
                      ->setName('user.consultations');
            }
            
            // New Consultation Form
            if (!RouteRegistry::isRegistered('GET', '/user/new-consultation')) {
                RouteRegistry::register('GET', '/user/new-consultation');
                $group->get('/new-consultation', [\App\Controllers\UserController::class, 'newConsultationForm'])
                      ->setName('user.new-consultation');
            }
            
            // Create Consultation
            if (!RouteRegistry::isRegistered('POST', '/user/consultation/create')) {
                RouteRegistry::register('POST', '/user/consultation/create');
                $group->post('/consultation/create', [\App\Controllers\UserController::class, 'createConsultation'])
                      ->setName('user.consultation.create');
            }
            
            // Cancel Consultation
            if (!RouteRegistry::isRegistered('POST', '/user/consultation/{id}/cancel')) {
                RouteRegistry::register('POST', '/user/consultation/{id}/cancel');
                $group->post('/consultation/{id}/cancel', [\App\Controllers\UserController::class, 'cancelConsultation'])
                      ->setName('user.consultation.cancel');
            }
            
            // Settings
            if (!RouteRegistry::isRegistered('GET', '/user/settings')) {
                RouteRegistry::register('GET', '/user/settings');
                $group->get('/settings', [\App\Controllers\UserController::class, 'settings'])
                      ->setName('user.settings');
            }
            
            // Update Settings
            if (!RouteRegistry::isRegistered('POST', '/user/settings/update')) {
                RouteRegistry::register('POST', '/user/settings/update');
                $group->post('/settings/update', [\App\Controllers\UserController::class, 'updateSettings'])
                      ->setName('user.settings.update');
            }
            
            // Orders
            if (!RouteRegistry::isRegistered('GET', '/user/orders')) {
                RouteRegistry::register('GET', '/user/orders');
                $group->get('/orders', [\App\Controllers\UserController::class, 'orders'])
                      ->setName('user.orders');
            }
            
            // Order Details
            if (!RouteRegistry::isRegistered('GET', '/user/orders/{id}')) {
                RouteRegistry::register('GET', '/user/orders/{id}');
                $group->get('/orders/{id}', [\App\Controllers\UserController::class, 'orderDetails'])
                      ->setName('user.orders.details');
            }
            
            // Notifications
            if (!RouteRegistry::isRegistered('GET', '/user/notifications')) {
                RouteRegistry::register('GET', '/user/notifications');
                $group->get('/notifications', [\App\Controllers\UserController::class, 'notifications'])
                      ->setName('user.notifications');
            }
        })->add(new AuthMiddleware($container));
        
        return $app;
    };
} else {
    // If already registered, return an empty function
    return function (App $app, ?ContainerInterface $container = null) {
        // Routes already registered, skipping...
        if (class_exists('\App\Utilities\Logger')) {
            Logger::debug('User routes already registered, skipping...');
        }
    };
}
