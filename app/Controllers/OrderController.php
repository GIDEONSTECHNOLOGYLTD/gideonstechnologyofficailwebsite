<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Core\Cache;
use PDO;

class OrderController
{
    protected $renderer;
    protected $db;
    protected $cache;
    protected $cacheEnabled = true;
    protected $cacheTTL = 1800; // 30 minutes cache lifetime
    
    public function __construct($container = null)
    {
        $this->renderer = $container->get('renderer') ?? null;
        $this->db = $container->get('db') ?? null;
        $this->cache = new Cache();
    }
    
    public function index(Request $request, Response $response)
    {
        // List user orders
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/orders/index.php', [
                'page' => 'my-orders',
                'title' => 'My Orders',
                'orders' => $this->getUserOrders()
            ]);
        }
        
        $response->getBody()->write('Your Orders');
        return $response;
    }
    
    public function show(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Get order details
        $order = $this->getOrderById($id);
        
        if (!$order) {
            return $response->withStatus(404);
        }
        
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/orders/show.php', [
                'page' => 'order-details',
                'title' => 'Order #' . $order['id'],
                'order' => $order
            ]);
        }
        
        $response->getBody()->write("Order #" . $order['id']);
        return $response;
    }
    
    public function cancel(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Cancel order logic
        // ...
        
        // Redirect back to orders
        return $response->withHeader('Location', '/gstore/orders')->withStatus(302);
    }
    
    // Admin methods
    public function adminIndex(Request $request, Response $response)
    {
        if ($this->renderer) {
            return $this->renderer->render($response, 'admin/store/orders/index.php', [
                'page' => 'admin-orders',
                'title' => 'Manage Orders',
                'orders' => $this->getAllOrders()
            ]);
        }
        
        $response->getBody()->write('Admin Orders Listing');
        return $response;
    }
    
    public function adminShow(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Get order details
        $order = $this->getOrderById($id);
        
        if (!$order) {
            return $response->withStatus(404);
        }
        
        if ($this->renderer) {
            return $this->renderer->render($response, 'admin/store/orders/show.php', [
                'page' => 'admin-order-details',
                'title' => 'Order #' . $order['id'],
                'order' => $order
            ]);
        }
        
        $response->getBody()->write("Admin Order #" . $order['id']);
        return $response;
    }
    
    public function updateStatus(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        
        // Update order status logic
        // ...
        
        // Redirect back to admin orders
        return $response->withHeader('Location', '/admin/store-orders')->withStatus(302);
    }
    
    // Helper methods with caching implementation
    protected function getUserOrders($userId = null)
    {
        // If no userId provided, try to get from session
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }
        
        if (!$userId) {
            return [];
        }
        
        $cacheKey = 'user_orders_' . $userId;
        
        // Try to get from cache first
        if ($this->cacheEnabled) {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        
        // If we have a database connection, fetch from there
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->prepare(
                    'SELECT o.*, oi.product_id, oi.quantity, oi.price, p.name as product_name 
                    FROM orders o 
                    LEFT JOIN order_items oi ON o.id = oi.order_id 
                    LEFT JOIN products p ON oi.product_id = p.id 
                    WHERE o.user_id = :user_id 
                    ORDER BY o.created_at DESC'
                );
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
                
                $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $orders = $this->processOrdersData($ordersData);
                
                // Store in cache for future requests
                if ($this->cacheEnabled) {
                    $this->cache->set($cacheKey, $orders, $this->cacheTTL);
                }
                
                return $orders;
            } catch (\Exception $e) {
                // Log the error but continue with fallback data
                error_log('Error fetching user orders: ' . $e->getMessage());
            }
        }
        
        // Fallback to static data if no DB or on error
        return [
            [
                'id' => 1001,
                'date' => '2023-10-15',
                'total' => 1299.99,
                'status' => 'delivered',
                'items' => [
                    ['name' => 'Laptop Pro X5', 'price' => 1299.99, 'quantity' => 1]
                ]
            ],
            [
                'id' => 1002,
                'date' => '2023-09-22',
                'total' => 149.99,
                'status' => 'shipped',
                'items' => [
                    ['name' => 'Wireless Headphones', 'price' => 149.99, 'quantity' => 1]
                ]
            ]
        ];
    }
    
    // Process raw order data from database
    protected function processOrdersData($ordersData)
    {
        $processedOrders = [];
        $currentOrderId = null;
        $currentOrder = null;
        
        foreach ($ordersData as $row) {
            if ($currentOrderId !== $row['id']) {
                // If we were processing an order, add it to the result
                if ($currentOrder !== null) {
                    $processedOrders[] = $currentOrder;
                }
                
                // Start a new order
                $currentOrderId = $row['id'];
                $currentOrder = [
                    'id' => $row['id'],
                    'date' => $row['created_at'] ?? date('Y-m-d'),
                    'total' => $row['total'] ?? 0,
                    'status' => $row['status'] ?? 'processing',
                    'items' => []
                ];
                
                // Add user info for admin view
                if (isset($row['user_id'])) {
                    $currentOrder['user_id'] = $row['user_id'];
                    // Could fetch user email here if needed
                }
            }
            
            // Add item to current order if product data exists
            if (isset($row['product_id']) && $row['product_id']) {
                $currentOrder['items'][] = [
                    'name' => $row['product_name'] ?? 'Unknown Product',
                    'price' => $row['price'] ?? 0,
                    'quantity' => $row['quantity'] ?? 1
                ];
            }
        }
        
        // Add the last order if exists
        if ($currentOrder !== null) {
            $processedOrders[] = $currentOrder;
        }
        
        return $processedOrders;
    }
    
    protected function getAllOrders()
    {
        $cacheKey = 'all_orders';
        
        // Try to get from cache first
        if ($this->cacheEnabled) {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        
        // If we have a database connection, fetch from there
        if ($this->db instanceof PDO) {
            try {
                $stmt = $this->db->query(
                    'SELECT o.*, u.email as user_email 
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id 
                    ORDER BY o.created_at DESC'
                );
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Store in cache for future requests
                if ($this->cacheEnabled) {
                    $this->cache->set($cacheKey, $orders, $this->cacheTTL);
                }
                
                return $orders;
            } catch (\Exception $e) {
                // Log the error but continue with fallback data
                error_log('Error fetching all orders: ' . $e->getMessage());
            }
        }
        
        // Fallback to static data if no DB or on error
        return [
            [
                'id' => 1001,
                'user' => 'john.doe@example.com',
                'date' => '2023-10-15',
                'total' => 1299.99,
                'status' => 'delivered'
            ],
            [
                'id' => 1002,
                'user' => 'jane.smith@example.com',
                'date' => '2023-09-22',
                'total' => 149.99,
                'status' => 'shipped'
            ],
            [
                'id' => 1003,
                'user' => 'bob.johnson@example.com',
                'date' => '2023-10-18',
                'total' => 799.99,
                'status' => 'processing'
            ]
        ];
    }
    
    protected function getOrderById($id)
    {
        $cacheKey = 'order_' . $id;
        
        // Try to get from cache first
        if ($this->cacheEnabled) {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        
        // If we have a database connection, fetch from there
        if ($this->db instanceof PDO) {
            try {
                // Get order details
                $stmt = $this->db->prepare(
                    'SELECT o.*, u.email as user_email 
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.id 
                    WHERE o.id = :id'
                );
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $order = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($order) {
                    // Get order items
                    $itemsStmt = $this->db->prepare(
                        'SELECT oi.*, p.name as product_name 
                        FROM order_items oi 
                        LEFT JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = :order_id'
                    );
                    $itemsStmt->bindParam(':order_id', $id, PDO::PARAM_INT);
                    $itemsStmt->execute();
                    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Format the order with items
                    $formattedOrder = [
                        'id' => $order['id'],
                        'date' => $order['created_at'] ?? date('Y-m-d'),
                        'total' => $order['total'] ?? 0,
                        'status' => $order['status'] ?? 'processing',
                        'user' => $order['user_email'] ?? 'Unknown',
                        'items' => []
                    ];
                    
                    foreach ($items as $item) {
                        $formattedOrder['items'][] = [
                            'name' => $item['product_name'] ?? 'Unknown Product',
                            'price' => $item['price'] ?? 0,
                            'quantity' => $item['quantity'] ?? 1
                        ];
                    }
                    
                    // Store in cache for future requests
                    if ($this->cacheEnabled) {
                        $this->cache->set($cacheKey, $formattedOrder, $this->cacheTTL);
                    }
                    
                    return $formattedOrder;
                }
            } catch (\Exception $e) {
                // Log the error but continue with fallback approach
                error_log('Error fetching order by ID: ' . $e->getMessage());
            }
        }
        
        // Fallback to the original method if no DB or on error
        // For user orders
        $userOrders = $this->getUserOrders();
        foreach ($userOrders as $order) {
            if ($order['id'] == $id) {
                // Cache the result
                if ($this->cacheEnabled) {
                    $this->cache->set($cacheKey, $order, $this->cacheTTL);
                }
                return $order;
            }
        }
        
        // For admin (all orders)
        $allOrders = $this->getAllOrders();
        foreach ($allOrders as $order) {
            if ($order['id'] == $id) {
                // Cache the result
                if ($this->cacheEnabled) {
                    $this->cache->set($cacheKey, $order, $this->cacheTTL);
                }
                return $order;
            }
        }
        
        return null;
    }
    
    // Method to invalidate cache when an order is updated
    protected function invalidateOrderCache($orderId, $userId = null)
    {
        // Invalidate specific order cache
        $this->cache->delete('order_' . $orderId);
        
        // Invalidate user orders cache if userId provided
        if ($userId) {
            $this->cache->delete('user_orders_' . $userId);
        }
        
        // Invalidate all orders cache
        $this->cache->delete('all_orders');
    }
}
