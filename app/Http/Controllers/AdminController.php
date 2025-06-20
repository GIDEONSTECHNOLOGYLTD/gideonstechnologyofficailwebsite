<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

/**
 * Admin Controller
 * 
 * Handles administrative routes and functionality
 */
class AdminController extends Controller
{
    /**
     * Display admin dashboard
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function dashboard(Request $request, Response $response): Response
    {
        try {
            // Check authentication
            if (!$this->isAuthenticated() || !$this->isAdmin()) {
                return $response
                    ->withHeader('Location', '/login?redirect=admin/dashboard')
                    ->withStatus(302);
            }
            
            return $this->render($response, 'admin/dashboard.php', [
                'page' => 'admin-dashboard',
                'stats' => $this->getDashboardStats()
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering admin dashboard: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the admin dashboard.');
        }
    }
    
    /**
     * Display users management page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function users(Request $request, Response $response): Response
    {
        try {
            // Check authentication
            if (!$this->isAuthenticated() || !$this->isAdmin()) {
                return $response
                    ->withHeader('Location', '/login?redirect=admin/users')
                    ->withStatus(302);
            }
            
            return $this->render($response, 'admin/users.php', [
                'page' => 'admin-users',
                'users' => $this->getAllUsers()
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering admin users page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the users management page.');
        }
    }
    
    /**
     * Display services management page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function services(Request $request, Response $response): Response
    {
        try {
            // Check authentication
            if (!$this->isAuthenticated() || !$this->isAdmin()) {
                return $response
                    ->withHeader('Location', '/login?redirect=admin/services')
                    ->withStatus(302);
            }
            
            return $this->render($response, 'admin/services.php', [
                'page' => 'admin-services',
                'services' => $this->getAllServices()
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering admin services page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the services management page.');
        }
    }
    
    /**
     * Display orders management page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function orders(Request $request, Response $response): Response
    {
        try {
            // Check authentication
            if (!$this->isAuthenticated() || !$this->isAdmin()) {
                return $response
                    ->withHeader('Location', '/login?redirect=admin/orders')
                    ->withStatus(302);
            }
            
            return $this->render($response, 'admin/orders.php', [
                'page' => 'admin-orders',
                'orders' => $this->getAllOrders()
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering admin orders page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the orders management page.');
        }
    }
    
    /**
     * Display settings page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function settings(Request $request, Response $response): Response
    {
        try {
            // Check authentication
            if (!$this->isAuthenticated() || !$this->isAdmin()) {
                return $response
                    ->withHeader('Location', '/login?redirect=admin/settings')
                    ->withStatus(302);
            }
            
            return $this->render($response, 'admin/settings.php', [
                'page' => 'admin-settings',
                'settings' => $this->getSettings()
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering admin settings page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the settings page.');
        }
    }
    
    /**
     * Handle settings update
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function updateSettings(Request $request, Response $response): Response
    {
        try {
            // Check authentication
            if (!$this->isAuthenticated() || !$this->isAdmin()) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }
            
            $data = $request->getParsedBody();
            
            // Validate settings
            $errors = [];
            
            if (empty($data['site_name'])) {
                $errors['site_name'] = 'Site name is required';
            }
            
            if (!empty($errors)) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ], 400);
            }
            
            // Save settings (in a real app, this would update the database)
            // For now, we'll just log the update
            if ($this->container->has('logger')) {
                $this->container->get('logger')->info('Settings updated', [
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'settings' => $data
                ]);
            }
            
            return $this->json($response, [
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error updating settings: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while updating settings'
            ], 500);
        }
    }
    
    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is an admin
     *
     * @return bool
     */
    private function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Get dashboard statistics
     *
     * @return array
     */
    private function getDashboardStats(): array
    {
        // In a real app, this would come from a database
        return [
            'users' => [
                'total' => 150,
                'new_this_month' => 23,
                'active' => 132
            ],
            'orders' => [
                'total' => 87,
                'pending' => 12,
                'completed' => 75
            ],
            'revenue' => [
                'total' => 28750.50,
                'this_month' => 3250.75,
                'last_month' => 4120.25
            ],
            'services' => [
                'total' => 5,
                'most_popular' => 'Web Development'
            ]
        ];
    }
    
    /**
     * Get all users
     *
     * @return array
     */
    private function getAllUsers(): array
    {
        // In a real app, this would come from a database
        return [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'admin',
                'created_at' => '2023-01-15',
                'status' => 'active'
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'role' => 'user',
                'created_at' => '2023-02-20',
                'status' => 'active'
            ],
            [
                'id' => 3,
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'role' => 'user',
                'created_at' => '2023-03-10',
                'status' => 'inactive'
            ],
            [
                'id' => 4,
                'name' => 'Alice Brown',
                'email' => 'alice@example.com',
                'role' => 'user',
                'created_at' => '2023-04-05',
                'status' => 'active'
            ],
            [
                'id' => 5,
                'name' => 'Charlie Wilson',
                'email' => 'charlie@example.com',
                'role' => 'user',
                'created_at' => '2023-05-12',
                'status' => 'active'
            ]
        ];
    }
    
    /**
     * Get all services
     *
     * @return array
     */
    private function getAllServices(): array
    {
        // In a real app, this would come from a database or service repository
        return [
            [
                'id' => 1,
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Professional websites and web applications',
                'full_description' => 'Our web development services include custom website design, e-commerce solutions, content management systems, and web application development.',
                'image' => 'webdev.jpg',
                'featured' => true,
                'pricing' => [
                    'basic' => 999,
                    'standard' => 2499,
                    'premium' => 4999
                ]
            ],
            [
                'id' => 2,
                'name' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'description' => 'iOS and Android mobile applications',
                'full_description' => 'We develop native and cross-platform mobile applications for iOS and Android.',
                'image' => 'mobileapp.jpg',
                'featured' => true,
                'pricing' => [
                    'basic' => 1999,
                    'standard' => 3999,
                    'premium' => 7999
                ]
            ],
            [
                'id' => 3,
                'name' => 'IT Consulting',
                'slug' => 'it-consulting',
                'description' => 'Strategic technology consulting services',
                'full_description' => 'Our IT consulting services help businesses optimize their technology infrastructure.',
                'image' => 'consulting.jpg',
                'featured' => true,
                'pricing' => [
                    'hourly' => 150,
                    'project' => 'Custom quote',
                    'retainer' => 'Starting at $2,500/month'
                ]
            ],
            [
                'id' => 4,
                'name' => 'Cloud Solutions',
                'slug' => 'cloud-solutions',
                'description' => 'Cloud migration and management',
                'full_description' => 'We help businesses leverage the power of cloud computing.',
                'image' => 'cloud.jpg',
                'featured' => false,
                'pricing' => [
                    'basic' => 1500,
                    'standard' => 3000,
                    'premium' => 5000
                ]
            ],
            [
                'id' => 5,
                'name' => 'Cybersecurity',
                'slug' => 'cybersecurity',
                'description' => 'Comprehensive security solutions',
                'full_description' => 'Our cybersecurity services protect your business from threats.',
                'image' => 'security.jpg',
                'featured' => false,
                'pricing' => [
                    'assessment' => 1200,
                    'implementation' => 'Starting at $2,500',
                    'monitoring' => 'Starting at $500/month'
                ]
            ]
        ];
    }
    
    /**
     * Get all orders
     *
     * @return array
     */
    private function getAllOrders(): array
    {
        // In a real app, this would come from a database
        return [
            [
                'id' => 1001,
                'user_name' => 'John Doe',
                'user_email' => 'john@example.com',
                'service' => 'Web Development - Premium',
                'amount' => 4999.00,
                'status' => 'completed',
                'created_at' => '2023-05-15',
                'payment_method' => 'credit_card'
            ],
            [
                'id' => 1002,
                'user_name' => 'Jane Smith',
                'user_email' => 'jane@example.com',
                'service' => 'Mobile App Development - Standard',
                'amount' => 3999.00,
                'status' => 'processing',
                'created_at' => '2023-06-20',
                'payment_method' => 'paypal'
            ],
            [
                'id' => 1003,
                'user_name' => 'Bob Johnson',
                'user_email' => 'bob@example.com',
                'service' => 'IT Consulting - 10 hours',
                'amount' => 1500.00,
                'status' => 'pending',
                'created_at' => '2023-07-05',
                'payment_method' => 'bank_transfer'
            ],
            [
                'id' => 1004,
                'user_name' => 'Alice Brown',
                'user_email' => 'alice@example.com',
                'service' => 'Cybersecurity - Assessment',
                'amount' => 1200.00,
                'status' => 'completed',
                'created_at' => '2023-07-12',
                'payment_method' => 'credit_card'
            ],
            [
                'id' => 1005,
                'user_name' => 'Charlie Wilson',
                'user_email' => 'charlie@example.com',
                'service' => 'Cloud Solutions - Basic',
                'amount' => 1500.00,
                'status' => 'refunded',
                'created_at' => '2023-07-18',
                'payment_method' => 'paypal'
            ]
        ];
    }
    
    /**
     * Get application settings
     *
     * @return array
     */
    private function getSettings(): array
    {
        // In a real app, this would come from a database or configuration file
        return [
            'site_name' => 'Gideon\'s Technology',
            'site_description' => 'Professional technology services for businesses',
            'contact_email' => 'contact@gideonstechnology.com',
            'support_email' => 'support@gideonstechnology.com',
            'address' => '123 Tech Street, Innovation City, IC 12345',
            'phone' => '+1 (123) 456-7890',
            'social_media' => [
                'facebook' => 'https://facebook.com/gideonstech',
                'twitter' => 'https://twitter.com/gideonstech',
                'linkedin' => 'https://linkedin.com/company/gideonstech',
                'instagram' => 'https://instagram.com/gideonstech'
            ],
            'notification_settings' => [
                'new_order_email' => true,
                'order_status_change_email' => true,
                'marketing_emails' => false
            ]
        ];
    }
}