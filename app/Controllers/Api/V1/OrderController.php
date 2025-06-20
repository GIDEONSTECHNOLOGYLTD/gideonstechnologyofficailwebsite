<?php

namespace App\Controllers\Api\V1;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Services\ValidationService;
use App\Utilities\Logger;

/**
 * API Order Controller
 * 
 * Handles all order-related API endpoints
 */
class OrderController
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
     * Get all orders
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
            $status = $params['status'] ?? null;
            
            // Ensure valid pagination values
            $page = max(1, $page);
            $limit = max(1, min(100, $limit));
            
            // In a real application, you would fetch orders from the database
            // For demonstration, we'll return simulated data
            $orders = [
                [
                    'id' => 1,
                    'user_id' => 2,
                    'product_id' => 1,
                    'status' => 'completed',
                    'total' => 999.99,
                    'payment_method' => 'credit_card',
                    'created_at' => '2023-01-15 10:30:00',
                    'completed_at' => '2023-02-20 14:45:00'
                ],
                [
                    'id' => 2,
                    'user_id' => 3,
                    'product_id' => 2,
                    'status' => 'in_progress',
                    'total' => 1499.99,
                    'payment_method' => 'paypal',
                    'created_at' => '2023-02-10 09:15:00',
                    'completed_at' => null
                ],
                [
                    'id' => 3,
                    'user_id' => 2,
                    'product_id' => 4,
                    'status' => 'pending',
                    'total' => 499.99,
                    'payment_method' => 'bank_transfer',
                    'created_at' => '2023-03-05 16:20:00',
                    'completed_at' => null
                ],
                [
                    'id' => 4,
                    'user_id' => 3,
                    'product_id' => 3,
                    'status' => 'cancelled',
                    'total' => 1999.99,
                    'payment_method' => 'credit_card',
                    'created_at' => '2023-03-10 11:45:00',
                    'cancelled_at' => '2023-03-12 08:30:00'
                ],
                [
                    'id' => 5,
                    'user_id' => 2,
                    'product_id' => 5,
                    'status' => 'completed',
                    'total' => 99.99,
                    'payment_method' => 'paypal',
                    'created_at' => '2023-04-01 13:10:00',
                    'completed_at' => '2023-04-01 13:15:00'
                ]
            ];
            
            // Filter by status if provided
            if ($status) {
                $orders = array_filter($orders, function($order) use ($status) {
                    return $order['status'] === $status;
                });
                // Reset array keys
                $orders = array_values($orders);
            }
            
            // Pagination metadata
            $total = count($orders);
            $totalPages = ceil($total / $limit);
            $offset = ($page - 1) * $limit;
            $items = array_slice($orders, $offset, $limit);
            
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
                            'next' => $page < $totalPages ? "/api/v1/orders?page=" . ($page + 1) . "&limit={$limit}" . ($status ? "&status={$status}" : '') : null,
                            'prev' => $page > 1 ? "/api/v1/orders?page=" . ($page - 1) . "&limit={$limit}" . ($status ? "&status={$status}" : '') : null
                        ]
                    ],
                    'filters' => [
                        'status' => $status
                    ]
                ]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API OrderController::index: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving orders.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Get order by ID
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            // Get order ID from route arguments
            $id = (int) $args['id'];
            
            // In a real application, you would fetch the order from the database
            // For demonstration, we'll use simulated data
            $orders = [
                1 => [
                    'id' => 1,
                    'user_id' => 2,
                    'product_id' => 1,
                    'status' => 'completed',
                    'total' => 999.99,
                    'payment_method' => 'credit_card',
                    'created_at' => '2023-01-15 10:30:00',
                    'completed_at' => '2023-02-20 14:45:00',
                    'user' => [
                        'id' => 2,
                        'name' => 'John Doe',
                        'email' => 'john@example.com'
                    ],
                    'product' => [
                        'id' => 1,
                        'name' => 'Website Development',
                        'price' => 999.99
                    ],
                    'timeline' => [
                        [
                            'status' => 'pending',
                            'date' => '2023-01-15 10:30:00',
                            'note' => 'Order created'
                        ],
                        [
                            'status' => 'in_progress',
                            'date' => '2023-01-20 09:15:00',
                            'note' => 'Work started on website'
                        ],
                        [
                            'status' => 'completed',
                            'date' => '2023-02-20 14:45:00',
                            'note' => 'Website completed and delivered'
                        ]
                    ]
                ],
                2 => [
                    'id' => 2,
                    'user_id' => 3,
                    'product_id' => 2,
                    'status' => 'in_progress',
                    'total' => 1499.99,
                    'payment_method' => 'paypal',
                    'created_at' => '2023-02-10 09:15:00',
                    'completed_at' => null,
                    'user' => [
                        'id' => 3,
                        'name' => 'Jane Smith',
                        'email' => 'jane@example.com'
                    ],
                    'product' => [
                        'id' => 2,
                        'name' => 'Mobile App Development',
                        'price' => 1499.99
                    ],
                    'timeline' => [
                        [
                            'status' => 'pending',
                            'date' => '2023-02-10 09:15:00',
                            'note' => 'Order created'
                        ],
                        [
                            'status' => 'in_progress',
                            'date' => '2023-02-15 11:30:00',
                            'note' => 'Work started on mobile app'
                        ]
                    ]
                ],
                3 => [
                    'id' => 3,
                    'user_id' => 2,
                    'product_id' => 4,
                    'status' => 'pending',
                    'total' => 499.99,
                    'payment_method' => 'bank_transfer',
                    'created_at' => '2023-03-05 16:20:00',
                    'completed_at' => null,
                    'user' => [
                        'id' => 2,
                        'name' => 'John Doe',
                        'email' => 'john@example.com'
                    ],
                    'product' => [
                        'id' => 4,
                        'name' => 'SEO Package',
                        'price' => 499.99
                    ],
                    'timeline' => [
                        [
                            'status' => 'pending',
                            'date' => '2023-03-05 16:20:00',
                            'note' => 'Order created, awaiting payment confirmation'
                        ]
                    ]
                ],
                4 => [
                    'id' => 4,
                    'user_id' => 3,
                    'product_id' => 3,
                    'status' => 'cancelled',
                    'total' => 1999.99,
                    'payment_method' => 'credit_card',
                    'created_at' => '2023-03-10 11:45:00',
                    'cancelled_at' => '2023-03-12 08:30:00',
                    'user' => [
                        'id' => 3,
                        'name' => 'Jane Smith',
                        'email' => 'jane@example.com'
                    ],
                    'product' => [
                        'id' => 3,
                        'name' => 'E-commerce Solution',
                        'price' => 1999.99
                    ],
                    'timeline' => [
                        [
                            'status' => 'pending',
                            'date' => '2023-03-10 11:45:00',
                            'note' => 'Order created'
                        ],
                        [
                            'status' => 'cancelled',
                            'date' => '2023-03-12 08:30:00',
                            'note' => 'Order cancelled by customer'
                        ]
                    ]
                ],
                5 => [
                    'id' => 5,
                    'user_id' => 2,
                    'product_id' => 5,
                    'status' => 'completed',
                    'total' => 99.99,
                    'payment_method' => 'paypal',
                    'created_at' => '2023-04-01 13:10:00',
                    'completed_at' => '2023-04-01 13:15:00',
                    'user' => [
                        'id' => 2,
                        'name' => 'John Doe',
                        'email' => 'john@example.com'
                    ],
                    'product' => [
                        'id' => 5,
                        'name' => 'Web Hosting',
                        'price' => 99.99
                    ],
                    'timeline' => [
                        [
                            'status' => 'pending',
                            'date' => '2023-04-01 13:10:00',
                            'note' => 'Order created'
                        ],
                        [
                            'status' => 'completed',
                            'date' => '2023-04-01 13:15:00',
                            'note' => 'Hosting account activated'
                        ]
                    ]
                ]
            ];
            
            // Check if order exists
            if (!isset($orders[$id])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Order not found.'
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $orders[$id]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API OrderController::show({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving the order.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Create a new order
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
            $this->validator->required($data, ['product_id', 'payment_method']);
            
            // Validate product_id is numeric
            if (isset($data['product_id'])) {
                $this->validator->numeric($data['product_id'], 'product_id');
            }
            
            // Validate payment method
            if (isset($data['payment_method'])) {
                $this->validator->inList($data['payment_method'], ['credit_card', 'paypal', 'bank_transfer'], 'payment_method');
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
            
            // In a real application, you would create the order in the database
            // For demonstration, we'll just return a success response with simulated data
            
            // Get product price (in a real app, this would come from the database)
            $productPrices = [
                1 => 999.99,
                2 => 1499.99,
                3 => 1999.99,
                4 => 499.99,
                5 => 99.99
            ];
            
            $productId = (int) $data['product_id'];
            $price = $productPrices[$productId] ?? 0;
            
            $order = [
                'id' => 6, // Simulated new ID
                'user_id' => 1, // Current authenticated user ID
                'product_id' => $productId,
                'status' => 'pending',
                'total' => $price,
                'payment_method' => $data['payment_method'],
                'created_at' => date('Y-m-d H:i:s'),
                'completed_at' => null
            ];
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $order,
                'message' => 'Order created successfully'
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201); // Created
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API OrderController::store: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while creating the order.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Update order
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            // Get order ID from route arguments
            $id = (int) $args['id'];
            
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['product_id', 'payment_method']);
            
            // Validate product_id is numeric
            if (isset($data['product_id'])) {
                $this->validator->numeric($data['product_id'], 'product_id');
            }
            
            // Validate payment method
            if (isset($data['payment_method'])) {
                $this->validator->inList($data['payment_method'], ['credit_card', 'paypal', 'bank_transfer'], 'payment_method');
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
            
            // In a real application, you would update the order in the database
            // For demonstration, we'll just return a success response with simulated data
            
            // Get product price (in a real app, this would come from the database)
            $productPrices = [
                1 => 999.99,
                2 => 1499.99,
                3 => 1999.99,
                4 => 499.99,
                5 => 99.99
            ];
            
            $productId = (int) $data['product_id'];
            $price = $productPrices[$productId] ?? 0;
            
            $order = [
                'id' => $id,
                'user_id' => 1, // Current authenticated user ID
                'product_id' => $productId,
                'status' => 'pending', // Status remains pending after update
                'total' => $price,
                'payment_method' => $data['payment_method'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $order,
                'message' => 'Order updated successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API OrderController::update({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while updating the order.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Update order status
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function updateStatus(Request $request, Response $response, array $args): Response
    {
        try {
            // Get order ID from route arguments
            $id = (int) $args['id'];
            
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['status']);
            
            // Validate status
            if (isset($data['status'])) {
                $this->validator->inList($data['status'], ['pending', 'in_progress', 'completed', 'cancelled'], 'status');
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
            
            // In a real application, you would update the order status in the database
            // For demonstration, we'll just return a success response with simulated data
            
            $status = $data['status'];
            $now = date('Y-m-d H:i:s');
            
            $statusFields = [
                'completed' => ['completed_at' => $now],
                'cancelled' => ['cancelled_at' => $now],
                'in_progress' => ['in_progress_at' => $now],
                'pending' => []
            ];
            
            $order = [
                'id' => $id,
                'status' => $status,
                'updated_at' => $now
            ];
            
            // Add status-specific timestamp
            if (isset($statusFields[$status])) {
                $order = array_merge($order, $statusFields[$status]);
            }
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $order,
                'message' => 'Order status updated successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API OrderController::updateStatus({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while updating the order status.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Delete order
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            // Get order ID from route arguments
            $id = (int) $args['id'];
            
            // In a real application, you would delete the order from the database
            // For demonstration, we'll just return a success response
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API OrderController::delete({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while deleting the order.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
