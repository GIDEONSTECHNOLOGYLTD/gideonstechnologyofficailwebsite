<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class TemplateController
{
    protected $renderer;
    
    public function __construct()
    {
        $this->renderer = new PhpRenderer('../templates');
    }
    
    /**
     * Display all available templates for purchase
     */
    public function index(Request $request, Response $response)
    {
        // In a real application, fetch templates from database
        $templates = [
            [
                'id' => 1,
                'name' => 'E-Commerce Template',
                'description' => 'A complete e-commerce solution for your business',
                'category' => 'E-Commerce',
                'price' => 49.99,
                'preview_image' => 'ecommerce-template.jpg',
                'demo_url' => 'https://demo.gideonstechnology.com/templates/ecommerce'
            ],
            [
                'id' => 2,
                'name' => 'Corporate Website',
                'description' => 'Professional corporate website template',
                'category' => 'Business',
                'price' => 39.99,
                'preview_image' => 'corporate-template.jpg',
                'demo_url' => 'https://demo.gideonstechnology.com/templates/corporate'
            ],
            [
                'id' => 3,
                'name' => 'Portfolio Template',
                'description' => 'Showcase your work with this elegant portfolio template',
                'category' => 'Portfolio',
                'price' => 29.99,
                'preview_image' => 'portfolio-template.jpg',
                'demo_url' => 'https://demo.gideonstechnology.com/templates/portfolio'
            ],
            [
                'id' => 4,
                'name' => 'School Management System',
                'description' => 'Complete school management solution',
                'category' => 'Education',
                'price' => 79.99,
                'preview_image' => 'school-template.jpg',
                'demo_url' => 'https://demo.gideonstechnology.com/templates/school'
            ]
        ];
        
        return $this->renderer->render($response, 'web-dev/templates.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'templates' => $templates
        ]);
    }
    
    /**
     * Display a specific template's details
     */
    public function show(Request $request, Response $response, array $args)
    {
        $id = $args['id'];
        
        // In a real application, fetch template from database
        $templateData = [
            'id' => $id,
            'name' => 'E-Commerce Template',
            'description' => 'A complete e-commerce solution for your business with product management, shopping cart, payment integration, and customer management.',
            'category' => 'E-Commerce',
            'price' => 49.99,
            'preview_image' => 'ecommerce-template.jpg',
            'demo_url' => 'https://demo.gideonstechnology.com/templates/ecommerce',
            'features' => [
                'Responsive design',
                'Product management',
                'Shopping cart',
                'Payment integration',
                'Customer accounts',
                'Order tracking',
                'Admin dashboard'
            ]
        ];
        
        return $this->renderer->render($response, 'web-dev/template-details.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'template' => $templateData
        ]);
    }
    
    /**
     * Process template purchase
     */
    public function purchase(Request $request, Response $response, array $args)
    {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        
        $id = $args['id'];
        
        // In a real application, process purchase in database
        // Record the template purchase with user ID and timestamp
        
        // Redirect to purchased templates page
        return $response->withHeader('Location', '/dashboard/templates')->withStatus(302);
    }
    
    /**
     * Display user's purchased templates
     */
    public function purchased(Request $request, Response $response)
    {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        
        // In a real application, fetch user's purchased templates from database
        $purchasedTemplates = [
            [
                'id' => 1,
                'name' => 'E-Commerce Template',
                'description' => 'A complete e-commerce solution for your business',
                'category' => 'E-Commerce',
                'preview_image' => 'ecommerce-template.jpg',
                'purchase_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'demo_url' => 'https://demo.gideonstechnology.com/templates/ecommerce'
            ],
            [
                'id' => 4,
                'name' => 'School Management System',
                'description' => 'Complete school management solution',
                'category' => 'Education',
                'preview_image' => 'school-template.jpg',
                'purchase_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'demo_url' => 'https://demo.gideonstechnology.com/templates/school'
            ]
        ];
        
        return $this->renderer->render($response, 'dashboard/templates.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'purchasedTemplates' => $purchasedTemplates
        ]);
    }
    
    /**
     * Download template files
     */
    public function download(Request $request, Response $response, array $args)
    {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        
        $id = $args['id'];
        
        // In a real application, verify user has purchased this template
        // and prepare download (zip file, etc.)
        
        // For demo purposes, redirect back to templates page
        // In a real app, this would trigger a file download
        return $response->withHeader('Location', '/dashboard/templates')->withStatus(302);
    }
}