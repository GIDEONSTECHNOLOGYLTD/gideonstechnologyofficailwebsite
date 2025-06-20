<?php

namespace App\Controllers\Api\V1;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Utilities\Logger;

/**
 * API Template Controller
 * 
 * Handles all template-related API endpoints
 */
class TemplateController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * Get all templates
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Get query parameters for filtering
            $params = $request->getQueryParams();
            $category = $params['category'] ?? null;
            
            // In a real application, you would fetch templates from the database
            // For demonstration, we'll return simulated data
            $templates = [
                [
                    'id' => 1,
                    'name' => 'Business Website',
                    'description' => 'Professional business website template with about, services, and contact pages',
                    'category' => 'business',
                    'thumbnail' => '/assets/images/templates/business.jpg',
                    'preview_url' => '/templates/preview/business',
                    'features' => [
                        'Responsive design',
                        'Contact form',
                        'Services showcase',
                        'Team members section'
                    ],
                    'price' => 0.00,
                    'is_premium' => false
                ],
                [
                    'id' => 2,
                    'name' => 'E-commerce Store',
                    'description' => 'Complete e-commerce template with product listings, cart, and checkout',
                    'category' => 'e-commerce',
                    'thumbnail' => '/assets/images/templates/ecommerce.jpg',
                    'preview_url' => '/templates/preview/ecommerce',
                    'features' => [
                        'Product catalog',
                        'Shopping cart',
                        'Checkout process',
                        'User accounts'
                    ],
                    'price' => 49.99,
                    'is_premium' => true
                ],
                [
                    'id' => 3,
                    'name' => 'Portfolio',
                    'description' => 'Showcase your work with this elegant portfolio template',
                    'category' => 'portfolio',
                    'thumbnail' => '/assets/images/templates/portfolio.jpg',
                    'preview_url' => '/templates/preview/portfolio',
                    'features' => [
                        'Project showcase',
                        'Filterable gallery',
                        'About me section',
                        'Contact form'
                    ],
                    'price' => 0.00,
                    'is_premium' => false
                ],
                [
                    'id' => 4,
                    'name' => 'Blog',
                    'description' => 'Clean and modern blog template with categories and comments',
                    'category' => 'blog',
                    'thumbnail' => '/assets/images/templates/blog.jpg',
                    'preview_url' => '/templates/preview/blog',
                    'features' => [
                        'Article listings',
                        'Categories',
                        'Comments section',
                        'Author profiles'
                    ],
                    'price' => 29.99,
                    'is_premium' => true
                ],
                [
                    'id' => 5,
                    'name' => 'Landing Page',
                    'description' => 'High-converting landing page template for products or services',
                    'category' => 'landing',
                    'thumbnail' => '/assets/images/templates/landing.jpg',
                    'preview_url' => '/templates/preview/landing',
                    'features' => [
                        'Call to action sections',
                        'Testimonials',
                        'Feature highlights',
                        'Pricing tables'
                    ],
                    'price' => 19.99,
                    'is_premium' => true
                ]
            ];
            
            // Filter by category if provided
            if ($category) {
                $templates = array_filter($templates, function($template) use ($category) {
                    return $template['category'] === $category;
                });
                // Reset array keys
                $templates = array_values($templates);
            }
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $templates
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API TemplateController::index: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving templates.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Get template by ID
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            // Get template ID from route arguments
            $id = (int) $args['id'];
            
            // In a real application, you would fetch the template from the database
            // For demonstration, we'll use simulated data
            $templates = [
                1 => [
                    'id' => 1,
                    'name' => 'Business Website',
                    'description' => 'Professional business website template with about, services, and contact pages',
                    'category' => 'business',
                    'thumbnail' => '/assets/images/templates/business.jpg',
                    'preview_url' => '/templates/preview/business',
                    'features' => [
                        'Responsive design',
                        'Contact form',
                        'Services showcase',
                        'Team members section'
                    ],
                    'pages' => [
                        'Home',
                        'About',
                        'Services',
                        'Team',
                        'Contact'
                    ],
                    'technologies' => [
                        'HTML5',
                        'CSS3',
                        'JavaScript',
                        'Bootstrap 5'
                    ],
                    'price' => 0.00,
                    'is_premium' => false,
                    'downloads' => 1250,
                    'rating' => 4.7,
                    'created_at' => '2023-01-10'
                ],
                2 => [
                    'id' => 2,
                    'name' => 'E-commerce Store',
                    'description' => 'Complete e-commerce template with product listings, cart, and checkout',
                    'category' => 'e-commerce',
                    'thumbnail' => '/assets/images/templates/ecommerce.jpg',
                    'preview_url' => '/templates/preview/ecommerce',
                    'features' => [
                        'Product catalog',
                        'Shopping cart',
                        'Checkout process',
                        'User accounts'
                    ],
                    'pages' => [
                        'Home',
                        'Products',
                        'Product Detail',
                        'Cart',
                        'Checkout',
                        'Account',
                        'Orders'
                    ],
                    'technologies' => [
                        'HTML5',
                        'CSS3',
                        'JavaScript',
                        'Bootstrap 5',
                        'jQuery'
                    ],
                    'price' => 49.99,
                    'is_premium' => true,
                    'downloads' => 875,
                    'rating' => 4.9,
                    'created_at' => '2023-02-15'
                ],
                3 => [
                    'id' => 3,
                    'name' => 'Portfolio',
                    'description' => 'Showcase your work with this elegant portfolio template',
                    'category' => 'portfolio',
                    'thumbnail' => '/assets/images/templates/portfolio.jpg',
                    'preview_url' => '/templates/preview/portfolio',
                    'features' => [
                        'Project showcase',
                        'Filterable gallery',
                        'About me section',
                        'Contact form'
                    ],
                    'pages' => [
                        'Home',
                        'Projects',
                        'Project Detail',
                        'About',
                        'Contact'
                    ],
                    'technologies' => [
                        'HTML5',
                        'CSS3',
                        'JavaScript',
                        'Bootstrap 5',
                        'Isotope.js'
                    ],
                    'price' => 0.00,
                    'is_premium' => false,
                    'downloads' => 2100,
                    'rating' => 4.5,
                    'created_at' => '2023-03-05'
                ],
                4 => [
                    'id' => 4,
                    'name' => 'Blog',
                    'description' => 'Clean and modern blog template with categories and comments',
                    'category' => 'blog',
                    'thumbnail' => '/assets/images/templates/blog.jpg',
                    'preview_url' => '/templates/preview/blog',
                    'features' => [
                        'Article listings',
                        'Categories',
                        'Comments section',
                        'Author profiles'
                    ],
                    'pages' => [
                        'Home',
                        'Blog',
                        'Article',
                        'Category',
                        'Author',
                        'About',
                        'Contact'
                    ],
                    'technologies' => [
                        'HTML5',
                        'CSS3',
                        'JavaScript',
                        'Bootstrap 5'
                    ],
                    'price' => 29.99,
                    'is_premium' => true,
                    'downloads' => 950,
                    'rating' => 4.6,
                    'created_at' => '2023-03-20'
                ],
                5 => [
                    'id' => 5,
                    'name' => 'Landing Page',
                    'description' => 'High-converting landing page template for products or services',
                    'category' => 'landing',
                    'thumbnail' => '/assets/images/templates/landing.jpg',
                    'preview_url' => '/templates/preview/landing',
                    'features' => [
                        'Call to action sections',
                        'Testimonials',
                        'Feature highlights',
                        'Pricing tables'
                    ],
                    'pages' => [
                        'Single page with sections'
                    ],
                    'technologies' => [
                        'HTML5',
                        'CSS3',
                        'JavaScript',
                        'Bootstrap 5',
                        'AOS (Animate On Scroll)'
                    ],
                    'price' => 19.99,
                    'is_premium' => true,
                    'downloads' => 1500,
                    'rating' => 4.8,
                    'created_at' => '2023-04-10'
                ]
            ];
            
            // Check if template exists
            if (!isset($templates[$id])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Template not found.'
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $templates[$id]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API TemplateController::show({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving the template.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
