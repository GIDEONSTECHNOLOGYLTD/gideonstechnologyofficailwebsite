<?php
/**
 * GTech Services Routes
 * 
 * This file defines all routes for the GTech services section using the GTechController.
 * Updated to use RouteRegistry to prevent duplicate route registrations.
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ServiceController;
use App\Core\RouteRegistry;

// Define a global flag to prevent duplicate loading
if (!defined('GTECH_SERVICES_ROUTES_REGISTERED')) {
    define('GTECH_SERVICES_ROUTES_REGISTERED', true);
    
    return function (App $app) {
        // Gtech services routes
        $app->group('/gtech', function (RouteCollectorProxy $group) {
            // Main Gtech pages
            if (!RouteRegistry::isRegistered('GET', '/gtech')) {
                RouteRegistry::register('GET', '/gtech');
                $group->get('', 'App\Controllers\GtechController:index')->setName('gtech.index');
            }
            
            if (!RouteRegistry::isRegistered('GET', '/gtech/about')) {
                RouteRegistry::register('GET', '/gtech/about');
                $group->get('/about', 'App\Controllers\GtechController:about')->setName('gtech.about');
            }
            
            if (!RouteRegistry::isRegistered('GET', '/gtech/contact')) {
                RouteRegistry::register('GET', '/gtech/contact');
                $group->get('/contact', 'App\Controllers\GtechController:contact')->setName('gtech.contact');
            }
            
            // Services group - ensure no duplicate routes
            $group->group('/services', function (RouteCollectorProxy $services) {
                // Main services page
                if (!RouteRegistry::isRegistered('GET', '/gtech/services')) {
                    RouteRegistry::register('GET', '/gtech/services');
                    $services->get('', 'App\Controllers\ServiceController:index')->setName('services.index');
                }
                
                // Repair services - ONLY ONE definition for this route
                if (!RouteRegistry::isRegistered('GET', '/gtech/services/repair')) {
                    RouteRegistry::register('GET', '/gtech/services/repair');
                    $services->get('/repair', 'App\Controllers\ServiceController:repair')->setName('services.repair');
                }
                
                // Specific repair service pages
                if (!RouteRegistry::isRegistered('GET', '/gtech/services/repair/computer')) {
                    RouteRegistry::register('GET', '/gtech/services/repair/computer');
                    $services->get('/repair/computer', 'App\Controllers\ServiceController:repairComputer')->setName('services.repair.computer');
                }
                
                if (!RouteRegistry::isRegistered('GET', '/gtech/services/repair/phone')) {
                    RouteRegistry::register('GET', '/gtech/services/repair/phone');
                    $services->get('/repair/phone', 'App\Controllers\ServiceController:repairPhone')->setName('services.repair.phone');
                }
                
                if (!RouteRegistry::isRegistered('GET', '/gtech/services/repair/tablet')) {
                    RouteRegistry::register('GET', '/gtech/services/repair/tablet');
                    $services->get('/repair/tablet', 'App\Controllers\ServiceController:repairTablet')->setName('services.repair.tablet');
                }
                
                // Other service categories
                if (!RouteRegistry::isRegistered('GET', '/gtech/services/development')) {
                    RouteRegistry::register('GET', '/gtech/services/development');
                    $services->get('/development', 'App\Controllers\ServiceController:development')->setName('services.development');
                }
                
                if (!RouteRegistry::isRegistered('GET', '/gtech/services/networking')) {
                    RouteRegistry::register('GET', '/gtech/services/networking');
                    $services->get('/networking', 'App\Controllers\ServiceController:networking')->setName('services.networking');
                }
                
                if (!RouteRegistry::isRegistered('GET', '/gtech/services/security')) {
                    RouteRegistry::register('GET', '/gtech/services/security');
                    $services->get('/security', 'App\Controllers\ServiceController:security')->setName('services.security');
                }
            });
        });
    };
} else {
    // If already registered, return an empty function
    return function (App $app) {
        // Routes already registered
        error_log('GTech Services routes already registered, skipping...');
    };
}