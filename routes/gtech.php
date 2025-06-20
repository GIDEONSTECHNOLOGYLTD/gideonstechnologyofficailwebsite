<?php
/**
 * Gideon's Technology GTech Routes
 * 
 * This file contains all routes related to GTech services platform
 * Uses RouteRegistry to prevent duplicate route registrations
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\GTechController;
use App\Core\RouteRegistry;
use App\Utilities\Logger;

// We'll let the main routes.php file handle the global flag
// This ensures consistent loading across the application

return function (App $app, $container = null) {
    // Get container if not provided
    if (!$container && method_exists($app, 'getContainer')) {
        $container = $app->getContainer();
    }
    
    // GTech home page
    if (!RouteRegistry::isRegistered('GET', '/gtech')) {
        RouteRegistry::register('GET', '/gtech');
        $app->get('/gtech', GTechController::class . ':index');
    }
    
    // GTech service details page
    if (!RouteRegistry::isRegistered('GET', '/gtech/service/{id}')) {
        RouteRegistry::register('GET', '/gtech/service/{id}');
        $app->get('/gtech/service/{id}', GTechController::class . ':service');
    }
    
    // GTech consultation form submission
    if (!RouteRegistry::isRegistered('POST', '/gtech/consultation')) {
        RouteRegistry::register('POST', '/gtech/consultation');
        $app->post('/gtech/consultation', GTechController::class . ':submitConsultation');
    }
    
    // GTech thank you page after consultation
    if (!RouteRegistry::isRegistered('GET', '/gtech/thank-you')) {
        RouteRegistry::register('GET', '/gtech/thank-you');
        $app->get('/gtech/thank-you', GTechController::class . ':thankYou');
    }
    
    // GTech services group
    $app->group('/gtech/services', function (RouteCollectorProxy $group) {
        // Business services
        if (!RouteRegistry::isRegistered('GET', '/gtech/services/business')) {
            RouteRegistry::register('GET', '/gtech/services/business');
            $group->get('/business', GTechController::class . ':businessServices');
        }
        
        // Individual services
        if (!RouteRegistry::isRegistered('GET', '/gtech/services/individual')) {
            RouteRegistry::register('GET', '/gtech/services/individual');
            $group->get('/individual', GTechController::class . ':individualServices');
        }
        
        // Tech repair service
        if (!RouteRegistry::isRegistered('GET', '/gtech/services/repair')) {
            RouteRegistry::register('GET', '/gtech/services/repair');
            $group->get('/repair', GTechController::class . ':repairService');
        }
        
        // All services page
        if (!RouteRegistry::isRegistered('GET', '/gtech/services')) {
            RouteRegistry::register('GET', '/gtech/services');
            $group->get('', GTechController::class . ':allServices');
        }
    });
    
    // GTech templates group
    $app->group('/gtech/templates', function (RouteCollectorProxy $group) {
        // All templates
        if (!RouteRegistry::isRegistered('GET', '/gtech/templates')) {
            RouteRegistry::register('GET', '/gtech/templates');
            $group->get('', GTechController::class . ':allTemplates');
        }
        
        // E-commerce templates
        if (!RouteRegistry::isRegistered('GET', '/gtech/templates/ecommerce')) {
            RouteRegistry::register('GET', '/gtech/templates/ecommerce');
            $group->get('/ecommerce', GTechController::class . ':ecommerceTemplates');
        }
        
        // Portfolio templates
        if (!RouteRegistry::isRegistered('GET', '/gtech/templates/portfolio')) {
            RouteRegistry::register('GET', '/gtech/templates/portfolio');
            $group->get('/portfolio', GTechController::class . ':portfolioTemplates');
        }
        
        // Individual template details
        if (!RouteRegistry::isRegistered('GET', '/gtech/templates/{id}')) {
            RouteRegistry::register('GET', '/gtech/templates/{id}');
            $group->get('/{id}', GTechController::class . ':templateDetails');
        }
    });
};
