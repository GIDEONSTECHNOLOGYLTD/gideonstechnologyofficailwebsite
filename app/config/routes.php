<?php

return [
    // Home routes
    '/' => ['HomeController', 'index'],
    
    // Auth routes
    '/login' => ['AuthController', 'login'],
    '/register' => ['AuthController', 'register'],
    '/logout' => ['AuthController', 'logout'],
    
    // Order routes
    '/orders' => ['OrderController', 'index'],
    '/orders/create/{id}' => ['OrderController', 'create'],
    '/orders/{id}' => ['OrderController', 'show'],
    
    // Payment routes
    '/payment/{id}' => ['PaymentController', 'show'],
    '/payment/process' => ['PaymentController', 'process'],
    '/payment/success' => ['PaymentController', 'success'],
    '/payment/cancel' => ['PaymentController', 'cancel'],
    
    // Service routes
    '/services' => ['ServicesController', 'index'],
    '/services/{id}' => ['ServicesController', 'show'],
    
    // Dashboard routes
    '/dashboard' => ['DashboardController', 'index'],
    '/dashboard/orders' => ['DashboardController', 'orders'],
    '/dashboard/payments' => ['DashboardController', 'payments'],
    
    // Admin routes
    '/admin/dashboard' => ['AdminController', 'index'],
    '/admin/users' => ['AdminController', 'users'],
    '/admin/products' => ['AdminController', 'products'],
    '/admin/orders' => ['AdminController', 'orders'],
    
    // Error routes
    '/error' => ['ErrorController', 'index'],
    '/404' => ['ErrorController', 'notFound'],
    '/500' => ['ErrorController', 'serverError']
];
