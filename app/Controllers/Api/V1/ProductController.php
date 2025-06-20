<?php

namespace App\Controllers\Api\V1;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Services\ValidationService;
use App\Utilities\Logger;

/**
 * API Product Controller
 * 
 * Handles all product-related API endpoints
 */
class ProductController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var ValidationService
     */
    protected $validator;
    
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = new ValidationService();
    }
    
    /**
     * Get all products
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Get query parameters for pagination
            $params = $request->getQueryParams();
            $page = isset($params['page']) ? (int) $params['page'] : 1;
            $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
            
            // Ensure valid pagination values
            $page = max(1, $page);
            $limit = max(1, min(100, $limit));
            
            // In a real application, you would fetch products from the database
            // For demonstration, we'll return simulated data
            $products = [
                [
                    'id' => 1,
                    'name' => 'Website Development',
                    'description' => 'Professional website development services',
                    'price' => 999.99,
                    'category' => 'web-development',
                    'featured' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Mobile App Development',
                    'description' => 'Custom mobile application development',
                    'price' => 1499.99,
                    'category' => 'app-development',
                    'featured' => true
                ],
                [
                    'id' => 3,
                    'name' => 'E-commerce Solution',
                    'description' => 'Complete e-commerce website setup',
                    'price' => 1999.99,
                    'category' => 'e-commerce',
                    'featured' => true
                ],
                [
                    'id' => 4,
                    'name' => 'SEO Package',
                    'description' => 'Search engine optimization services',
                    'price' => 499.99,
                    'category' => 'marketing',
                    'featured' => false
                ],
                [
                    'id' => 5,
                    'name' => 'Web Hosting',
                    'description' => 'Reliable web hosting services',
                    'price' => 99.99,
                    'category' => 'hosting',
                    'featured' => false
                ]
            ];
            
            // Pagination metadata
            $total = count($products);
            $totalPages = ceil($total / $limit);
            $offset = ($page - 1) * $limit;
            $items = array_slice($products, $offset, $limit);
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $items,
                'meta' => [
                    'pagination' => [
                        'total' => $total,
                        'count' => count($items),
                        'per_page' => $limit,
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'links' => [
                            'next' => $page < $totalPages ? "/api/v1/products?page=" . ($page + 1) . "&limit={$limit}" : null,
                            'prev' => $page > 1 ? "/api/v1/products?page=" . ($page - 1) . "&limit={$limit}" : null
                        ]
                    ]
                ]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API ProductController::index: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving products.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Search products
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function search(Request $request, Response $response): Response
    {
        try {
            // Get query parameters
            $params = $request->getQueryParams();
            $query = $params['q'] ?? '';
            $category = $params['category'] ?? null;
            
            // In a real application, you would search products in the database
            // For demonstration, we'll filter simulated data
            $products = [
                [
                    'id' => 1,
                    'name' => 'Website Development',
                    'description' => 'Professional website development services',
                    'price' => 999.99,
                    'category' => 'web-development',
                    'featured' => true
                ],
                [
                    'id' => 2,
                    'name' => 'Mobile App Development',
                    'description' => 'Custom mobile application development',
                    'price' => 1499.99,
                    'category' => 'app-development',
                    'featured' => true
                ],
                [
                    'id' => 3,
                    'name' => 'E-commerce Solution',
                    'description' => 'Complete e-commerce website setup',
                    'price' => 1999.99,
                    'category' => 'e-commerce',
                    'featured' => true
                ],
                [
                    'id' => 4,
                    'name' => 'SEO Package',
                    'description' => 'Search engine optimization services',
                    'price' => 499.99,
                    'category' => 'marketing',
                    'featured' => false
                ],
                [
                    'id' => 5,
                    'name' => 'Web Hosting',
                    'description' => 'Reliable web hosting services',
                    'price' => 99.99,
                    'category' => 'hosting',
                    'featured' => false
                ]
            ];
            
            // Filter by search query
            if (!empty($query)) {
                $query = strtolower($query);
                $products = array_filter($products, function($product) use ($query) {
                    return strpos(strtolower($product['name']), $query) !== false ||
                           strpos(strtolower($product['description']), $query) !== false;
                });
            }
            
            // Filter by category
            if (!empty($category)) {
                $products = array_filter($products, function($product) use ($category) {
                    return $product['category'] === $category;
                });
            }
            
            // Reset array keys
            $products = array_values($products);
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $products,
                'meta' => [
                    'total' => count($products),
                    'query' => $query,
                    'category' => $category
                ]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API ProductController::search: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while searching products.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Get product by ID
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            // Get product ID from route arguments
            $id = (int) $args['id'];
            
            // In a real application, you would fetch the product from the database
            // For demonstration, we'll use simulated data
            $products = [
                1 => [
                    'id' => 1,
                    'name' => 'Website Development',
                    'description' => 'Professional website development services',
                    'price' => 999.99,
                    'category' => 'web-development',
                    'featured' => true,
                    'details' => [
                        'includes' => [
                            'Responsive design',
                            'Content management system',
                            'Contact form',
                            'Basic SEO setup',
                            '1 year hosting'
                        ],
                        'delivery_time' => '4-6 weeks',
                        'revisions' => 3
                    ]
                ],
                2 => [
                    'id' => 2,
                    'name' => 'Mobile App Development',
                    'description' => 'Custom mobile application development',
                    'price' => 1499.99,
                    'category' => 'app-development',
                    'featured' => true,
                    'details' => [
                        'includes' => [
                            'iOS and Android versions',
                            'User authentication',
                            'Push notifications',
                            'API integration',
                            'App store submission'
                        ],
                        'delivery_time' => '8-12 weeks',
                        'revisions' => 5
                    ]
                ],
                3 => [
                    'id' => 3,
                    'name' => 'E-commerce Solution',
                    'description' => 'Complete e-commerce website setup',
                    'price' => 1999.99,
                    'category' => 'e-commerce',
                    'featured' => true,
                    'details' => [
                        'includes' => [
                            'Product catalog',
                            'Shopping cart',
                            'Payment gateway integration',
                            'Order management',
                            'Customer accounts'
                        ],
                        'delivery_time' => '6-8 weeks',
                        'revisions' => 4
                    ]
                ],
                4 => [
                    'id' => 4,
                    'name' => 'SEO Package',
                    'description' => 'Search engine optimization services',
                    'price' => 499.99,
                    'category' => 'marketing',
                    'featured' => false,
                    'details' => [
                        'includes' => [
                            'Keyword research',
                            'On-page optimization',
                            'Content strategy',
                            'Backlink building',
                            'Monthly reporting'
                        ],
                        'delivery_time' => 'Ongoing',
                        'duration' => '3 months'
                    ]
                ],
                5 => [
                    'id' => 5,
                    'name' => 'Web Hosting',
                    'description' => 'Reliable web hosting services',
                    'price' => 99.99,
                    'category' => 'hosting',
                    'featured' => false,
                    'details' => [
                        'includes' => [
                            '10GB storage',
                            'Unlimited bandwidth',
                            '5 email accounts',
                            'SSL certificate',
                            '24/7 support'
                        ],
                        'duration' => '1 year',
                        'renewal' => 99.99
                    ]
                ]
            ];
            
            // Check if product exists
            if (!isset($products[$id])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Product not found.'
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $products[$id]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API ProductController::show({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving the product.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Create a new product
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        try {
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['name', 'description', 'price', 'category']);
            
            // Validate price is numeric
            if (isset($data['price'])) {
                $this->validator->numeric($data['price'], 'price');
            }
            
            // Check for validation errors
            if ($this->validator->hasErrors()) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'validation_error',
                        'message' => 'Validation failed',
                        'fields' => $this->validator->getErrors()
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
            }
            
            // In a real application, you would create the product in the database
            // For demonstration, we'll just return a success response with simulated data
            
            $product = [
                'id' => 6, // Simulated new ID
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => (float) $data['price'],
                'category' => $data['category'],
                'featured' => isset($data['featured']) ? (bool) $data['featured'] : false,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $product,
                'message' => 'Product created successfully'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201); // Created
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API ProductController::store: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while creating the product.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Update product
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            // Get product ID from route arguments
            $id = (int) $args['id'];
            
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['name', 'description', 'price', 'category']);
            
            // Validate price is numeric
            if (isset($data['price'])) {
                $this->validator->numeric($data['price'], 'price');
            }
            
            // Check for validation errors
            if ($this->validator->hasErrors()) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'validation_error',
                        'message' => 'Validation failed',
                        'fields' => $this->validator->getErrors()
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
            }
            
            // In a real application, you would update the product in the database
            // For demonstration, we'll just return a success response with simulated data
            
            $product = [
                'id' => $id,
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => (float) $data['price'],
                'category' => $data['category'],
                'featured' => isset($data['featured']) ? (bool) $data['featured'] : false,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $product,
                'message' => 'Product updated successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API ProductController::update({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while updating the product.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Delete product
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            // Get product ID from route arguments
            $id = (int) $args['id'];
            
            // In a real application, you would delete the product from the database
            // For demonstration, we'll just return a success response
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API ProductController::delete({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while deleting the product.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
