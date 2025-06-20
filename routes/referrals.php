<?php

/**
 * Referral System Routes
 * 
 * This file defines all routes for the referral system including user dashboard
 * and admin management interfaces.
 * Uses RouteRegistry to prevent duplicate route registrations
 */

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\ReferralController;
use App\Core\RouteRegistry;
use App\Core\RouteManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

// Define a global flag to prevent duplicate loading
if (!defined('REFERRAL_ROUTES_REGISTERED')) {
    define('REFERRAL_ROUTES_REGISTERED', true);
    
    return function (App $app, ?ContainerInterface $container = null) {
        // Create a RouteManager instance
        $routeManager = new RouteManager($app, $container);
        
        // Get middleware from container
        $authMiddleware = $container->get('auth_middleware');
        $adminAuthMiddleware = $container->get('admin_auth_middleware');
        
        // Public routes
        $routeManager->get('/refer/{code}', ['App\Controllers\ReferralController', 'processReferral'], 'referral.process', 'default');

        // User referral dashboard routes - protected by auth middleware
        $routeManager->group('/referrals', function ($group) use ($routeManager) {
            $routeManager->get('', ['App\Controllers\ReferralController', 'dashboard'], 'referral.dashboard', 'html');
            $routeManager->get('/history', ['App\Controllers\ReferralController', 'history'], 'referral.history', 'html');
        }, [$authMiddleware]);

        // Admin referral management routes - protected by admin middleware
        $routeManager->group('/admin/referrals', function ($group) use ($routeManager) {
            $routeManager->get('', ['App\Controllers\ReferralController', 'adminDashboard'], 'admin.referral.dashboard', 'html');
            $routeManager->post('/settings', ['App\Controllers\ReferralController', 'saveSettings'], 'admin.referral.settings', 'json');
            $routeManager->post('/approve/{id}', ['App\Controllers\ReferralController', 'approveReward'], 'admin.referral.approve', 'json');
            $routeManager->post('/reject/{id}', ['App\Controllers\ReferralController', 'rejectReward'], 'admin.referral.reject', 'json');
            $routeManager->get('/export', ['App\Controllers\ReferralController', 'exportData'], 'admin.referral.export', 'download');
        }, [$adminAuthMiddleware]);
    };
} else {
    // If already registered, return an empty function
    return function (App $app, ?ContainerInterface $container = null) {
        // Routes already registered
        error_log('Referral routes already registered, skipping...');
    };
}
