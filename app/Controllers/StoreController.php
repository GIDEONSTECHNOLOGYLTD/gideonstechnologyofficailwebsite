<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Flash\Messages;
use Slim\Views\Twig;

/**
 * Store Controller
 * 
 * Handles all store related requests
 */
class StoreController
{
    /**
     * @var TwigView
     */
    protected $view;

    /**
     * @var Messages
     */
    protected $flash;

    /**
     * @var PhpRenderer
     */
    protected $renderer;

    /**
     * @var Product
     */
    protected $productModel;

    /**
     * @var Category
     */
    protected $categoryModel;

    /**
     * @var Cart
     */
    protected $cartModel;

    /**
     * @var User
     */
    protected $userModel;

    /**
     * @var Order
     */
    protected $orderModel;

    /**
     * Constructor
     * 
     * @param \Slim\Views\Twig $view View renderer
     * @param Messages $flash Flash messages
     * @param PhpRenderer $renderer PHP renderer for templates
     */
    public function __construct($container = null)
    {
        // Define base path if not already defined
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__, 2));
        }
        
        // Get dependencies from container if available
        if ($container) {
            $this->view = $container->get('twig.view') ?? null;
            $this->flash = $container->get('flash') ?? null;
            $this->renderer = $container->get('renderer') ?? null;
            $this->db = $container->get('db') ?? null;
        }
        
        // Fallback for renderer if not available in container
        if (!$this->renderer) {
            $this->renderer = new PhpRenderer(BASE_PATH . '/resources/views/');
        }
    }
    
    /**
     * Store index page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Use static data for now until database models are fully implemented
            $products = $this->getDummyProducts();
            $categories = $this->getDummyCategories();
            
            // Add title to the view data
            $this->setViewData('title', 'Store - ' . ($this->config['app.name'] ?? 'Gideon\'s Technology'));
            
            // Add flash message if no products found
            if (empty($products)) {
                $this->flash->addMessage('info', 'No products found in the store.');
            }
            
            // Render the store index view using the base controller's render method
            return $this->renderer->render($response, 'store/index.php', [
                'products' => $products,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            // Log error
            error_log('Store index error: ' . $e->getMessage());
            
            // Return error page
            return $this->renderer->render($response, 'error/500.php', [
                'title' => 'Error',
                'error' => $e->getMessage()
            ])->withStatus(500);
        }
    }
    
    /**
     * Sales report for admin
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered sales report
     */
    /**
     * Get dummy products for testing
     * 
     * @return array
     */
    private function getDummyProducts(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Smartphone X',
                'description' => 'Latest smartphone with advanced features',
                'price' => 799.99,
                'image' => '/assets/images/products/smartphone.jpg',
                'category_id' => 1
            ],
            [
                'id' => 2,
                'name' => 'Laptop Pro',
                'description' => 'High-performance laptop for professionals',
                'price' => 1299.99,
                'image' => '/assets/images/products/laptop.jpg',
                'category_id' => 1
            ],
            [
                'id' => 3,
                'name' => 'Wireless Headphones',
                'description' => 'Premium noise-cancelling headphones',
                'price' => 249.99,
                'image' => '/assets/images/products/headphones.jpg',
                'category_id' => 2
            ],
            [
                'id' => 4,
                'name' => 'Smart Watch',
                'description' => 'Fitness and health tracking smartwatch',
                'price' => 199.99,
                'image' => '/assets/images/products/smartwatch.jpg',
                'category_id' => 2
            ]
        ];
    }
    
    /**
     * Get dummy categories for testing
     * 
     * @return array
     */
    private function getDummyCategories(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Computers & Laptops',
                'description' => 'Desktops, laptops, and accessories'
            ],
            [
                'id' => 2,
                'name' => 'Audio & Wearables',
                'description' => 'Headphones, earbuds, and wearable technology'
            ],
            [
                'id' => 3,
                'name' => 'Smartphones & Tablets',
                'description' => 'Mobile phones, tablets, and accessories'
            ]
        ];
    }

    public function salesReport(Request $request, Response $response): Response
    {
        // Get query parameters
        $params = $request->getQueryParams();
        $startDate = $params['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $params['end_date'] ?? date('Y-m-d');
        
        // Get sales data
        $salesData = $this->orderModel->findAllBetweenDates($startDate, $endDate);
        
        // Get totals
        $totals = $this->orderModel->calculateSalesTotals($startDate, $endDate);
        
        return $this->view->render($response, 'admin/reports/sales.twig', [
            'sales_data' => $salesData,
            'totals' => $totals,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
    
    /**
     * Product report for admin
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered product report
     */
    public function productReport(Request $request, Response $response): Response
    {
        // Get top selling products
        $topProducts = $this->productModel->findBestSelling(20);
        
        // Get products with low stock
        $lowStock = $this->productModel->findAllWhere(['stock_quantity <= ' => 10]);
        
        return $this->view->render($response, 'admin/reports/products.twig', [
            'top_products' => $topProducts,
            'low_stock' => $lowStock
        ]);
    }
    
    /**
     * Customer report for admin
     * 
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Rendered customer report
     */
    public function customerReport(Request $request, Response $response): Response
    {
        // Get top customers by order value
        $topCustomers = $this->userModel->findTopByOrderValue(20);
        
        // Get new customers
        $newCustomers = $this->userModel->findAllOrderBy('created_at', 'DESC', 10);
        
        return $this->view->render($response, 'admin/reports/customers.twig', [
            'top_customers' => $topCustomers,
            'new_customers' => $newCustomers
        ]);
    }

    /**
     * Get popular products
     * 
     * @param int $limit Number of products to return
     * @return array Popular products
     */
    public function getPopularProducts(int $limit = 8): array
    {
        return $this->productModel->findAllOrderBy('sales_count', 'DESC', $limit);
    }

    public function viewCart(Request $request, Response $response)
    {
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/cart.php', [
                'page' => 'cart',
                'title' => 'Shopping Cart'
            ]);
        }
        
        $response->getBody()->write('Shopping Cart');
        return $response;
    }
    
    public function addToCart(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        // Cart logic would go here
        
        // Redirect back to cart
        return $response->withHeader('Location', '/gstore/cart')->withStatus(302);
    }
    
    public function updateCart(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        // Update cart logic

        // Redirect back to cart
        return $response->withHeader('Location', '/gstore/cart')->withStatus(302);
    }
    
    public function removeFromCart(Request $request, Response $response, $args)
    {
        $id = $args['id'] ?? null;
        // Remove from cart logic
        
        // Redirect back to cart
        return $response->withHeader('Location', '/gstore/cart')->withStatus(302);
    }
    
    public function checkout(Request $request, Response $response)
    {
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/checkout.php', [
                'page' => 'checkout',
                'title' => 'Checkout'
            ]);
        }
        
        $response->getBody()->write('Checkout');
        return $response;
    }
    
    public function placeOrder(Request $request, Response $response)
    {
        // Order processing logic
        $orderId = time(); // For demo purposes
        
        return $response->withHeader('Location', "/gstore/checkout/confirmation/{$orderId}")->withStatus(302);
    }
    
    public function confirmation(Request $request, Response $response, $args)
    {
        $orderId = $args['orderId'] ?? null;
        
        if ($this->renderer) {
            return $this->renderer->render($response, 'gstore/confirmation.php', [
                'page' => 'confirmation',
                'title' => 'Order Confirmation',
                'orderId' => $orderId
            ]);
        }
        
        $response->getBody()->write("Order Confirmation: {$orderId}");
        return $response;
    }
}