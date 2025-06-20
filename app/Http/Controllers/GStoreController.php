<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

/**
 * GStore Controller
 * 
 * Handles GStore e-commerce routes and functionality
 */
class GStoreController extends Controller
{
    /**
     * Display GStore home page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function home(Request $request, Response $response): Response
    {
        try {
            return $this->render($response, 'gstore/index.php', [
                'page' => 'gstore',
                'featuredProducts' => $this->getFeaturedProducts()
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering GStore home page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the GStore home page.');
        }
    }
    
    /**
     * Display products listing page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function products(Request $request, Response $response): Response
    {
        try {
            return $this->render($response, 'gstore/products.php', [
                'page' => 'gstore-products',
                'products' => $this->getAllProducts(),
                'categories' => $this->getCategories()
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering products page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the products page.');
        }
    }
    
    /**
     * Display product detail page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function productDetail(Request $request, Response $response, array $args): Response
    {
        $productId = $args['id'] ?? 0;
        
        try {
            $product = $this->getProductById($productId);
            
            if (!$product) {
                return $this->notFound($response, 'Product not found.');
            }
            
            return $this->render($response, 'gstore/product.php', [
                'page' => 'gstore-product',
                'product' => $product,
                'relatedProducts' => $this->getRelatedProducts($productId)
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering product detail page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the product detail page.');
        }
    }
    
    /**
     * Display category listing page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function category(Request $request, Response $response, array $args): Response
    {
        $category = $args['category'] ?? '';
        
        try {
            $products = $this->getProductsByCategory($category);
            
            return $this->render($response, 'gstore/category.php', [
                'page' => 'gstore-category',
                'category' => ucfirst($category),
                'products' => $products
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering category page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the category page.');
        }
    }
    
    /**
     * Display shopping cart page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function cart(Request $request, Response $response): Response
    {
        try {
            // Get cart items
            $cartItems = [];
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $productId => $quantity) {
                    $product = $this->getProductById($productId);
                    if ($product) {
                        $cartItems[] = [
                            'product' => $product,
                            'quantity' => $quantity,
                            'total' => $product['price'] * $quantity
                        ];
                    }
                }
            }
            
            $cartTotal = array_sum(array_map(function($item) {
                return $item['total'];
            }, $cartItems));
            
            return $this->render($response, 'gstore/cart.php', [
                'page' => 'gstore-cart',
                'cartItems' => $cartItems,
                'cartTotal' => $cartTotal
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering cart page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the cart page.');
        }
    }
    
    /**
     * Display checkout page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function checkout(Request $request, Response $response): Response
    {
        try {
            // Check if user is logged in
            if (!$this->isAuthenticated()) {
                $_SESSION['redirect_after_login'] = '/gstore/checkout';
                return $response->withHeader('Location', '/login')->withStatus(302);
            }
            
            // Get cart items
            $cartItems = [];
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $productId => $quantity) {
                    $product = $this->getProductById($productId);
                    if ($product) {
                        $cartItems[] = [
                            'product' => $product,
                            'quantity' => $quantity,
                            'total' => $product['price'] * $quantity
                        ];
                    }
                }
            }
            
            $cartTotal = array_sum(array_map(function($item) {
                return $item['total'];
            }, $cartItems));
            
            return $this->render($response, 'gstore/checkout.php', [
                'page' => 'gstore-checkout',
                'cartItems' => $cartItems,
                'cartTotal' => $cartTotal
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering checkout page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the checkout page.');
        }
    }
    
    /**
     * Display wishlist page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function wishlist(Request $request, Response $response): Response
    {
        try {
            // Check if user is logged in
            if (!$this->isAuthenticated()) {
                $_SESSION['redirect_after_login'] = '/gstore/wishlist';
                return $response->withHeader('Location', '/login')->withStatus(302);
            }
            
            // In a real app, this would fetch the user's wishlist from database
            $wishlistItems = [
                [
                    'product' => $this->getProductById(1),
                    'added_at' => '2023-07-01'
                ],
                [
                    'product' => $this->getProductById(3),
                    'added_at' => '2023-07-15'
                ]
            ];
            
            return $this->render($response, 'gstore/wishlist.php', [
                'page' => 'gstore-wishlist',
                'wishlistItems' => $wishlistItems
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering wishlist page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the wishlist page.');
        }
    }
    
    /**
     * Process checkout and create order
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function processCheckout(Request $request, Response $response): Response
    {
        try {
            // Check if user is logged in
            if (!$this->isAuthenticated()) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'You must be logged in to complete checkout'
                ], 401);
            }
            
            // Check if cart has items
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }
            
            $data = $request->getParsedBody();
            
            // In a real app, you would process payment and create an order in the database
            // For now, we'll just log the order and clear the cart
            if ($this->container->has('logger')) {
                $this->container->get('logger')->info('Order placed', [
                    'user_id' => $_SESSION['user_id'],
                    'cart' => $_SESSION['cart']
                ]);
            }
            
            // Clear the cart after successful order
            $_SESSION['cart'] = [];
            
            // Create a unique order ID
            $orderId = uniqid('ORD-');
            
            // Store order ID in session for confirmation page
            $_SESSION['last_order_id'] = $orderId;
            
            return $this->json($response, [
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $orderId,
                'redirect' => '/gstore/order/confirmation'
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error processing checkout: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while processing your order. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Display order confirmation page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function orderConfirmation(Request $request, Response $response): Response
    {
        try {
            // Check if user is logged in
            if (!$this->isAuthenticated()) {
                return $response->withHeader('Location', '/login')->withStatus(302);
            }
            
            $orderId = $_SESSION['last_order_id'] ?? 'N/A';
            
            return $this->render($response, 'gstore/order-confirmation.php', [
                'page' => 'gstore-order-confirmation',
                'orderId' => $orderId
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error rendering order confirmation page: ' . $e->getMessage());
            }
            return $this->serverError($response, 'An error occurred while loading the order confirmation page.');
        }
    }
    
    /**
     * Add product to cart API endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function addToCart(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Check if the request is JSON
            if (empty($data) && $request->getHeaderLine('Content-Type') === 'application/json') {
                $data = json_decode($request->getBody()->getContents(), true);
            }
            
            $productId = $data['product_id'] ?? null;
            $quantity = (int)($data['quantity'] ?? 1);
            
            if (!$productId) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Product ID is required'
                ], 400);
            }
            
            if ($quantity <= 0) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Quantity must be greater than zero'
                ], 400);
            }
            
            // Verify that product exists
            $product = $this->getProductById($productId);
            if (!$product) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            // Initialize cart if it doesn't exist
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Add to cart or update quantity
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = $quantity;
            }
            
            // Log the action
            if ($this->container->has('logger')) {
                $this->container->get('logger')->info('Product added to cart', [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'user_id' => $_SESSION['user_id'] ?? 'guest'
                ]);
            }
            
            return $this->json($response, [
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => array_sum($_SESSION['cart'])
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error adding product to cart: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while adding product to cart'
            ], 500);
        }
    }
    
    /**
     * Get cart count API endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCartCount(Request $request, Response $response): Response
    {
        $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
        
        return $this->json($response, [
            'success' => true,
            'cart_count' => $cartCount
        ]);
    }
    
    /**
     * Remove product from cart API endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function removeFromCart(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Check if the request is JSON
            if (empty($data) && $request->getHeaderLine('Content-Type') === 'application/json') {
                $data = json_decode($request->getBody()->getContents(), true);
            }
            
            $productId = $data['product_id'] ?? null;
            
            if (!$productId || !isset($_SESSION['cart']) || !isset($_SESSION['cart'][$productId])) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Product not found in cart'
                ], 400);
            }
            
            // Remove from cart
            unset($_SESSION['cart'][$productId]);
            
            // Log the action
            if ($this->container->has('logger')) {
                $this->container->get('logger')->info('Product removed from cart', [
                    'product_id' => $productId,
                    'user_id' => $_SESSION['user_id'] ?? 'guest'
                ]);
            }
            
            return $this->json($response, [
                'success' => true,
                'message' => 'Product removed from cart',
                'cart_count' => isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error removing product from cart: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while removing product from cart'
            ], 500);
        }
    }
    
    /**
     * Update cart quantity API endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function updateCart(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Check if the request is JSON
            if (empty($data) && $request->getHeaderLine('Content-Type') === 'application/json') {
                $data = json_decode($request->getBody()->getContents(), true);
            }
            
            $productId = $data['product_id'] ?? null;
            $quantity = (int)($data['quantity'] ?? 0);
            
            if (!$productId || !isset($_SESSION['cart']) || !isset($_SESSION['cart'][$productId])) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Product not found in cart'
                ], 400);
            }
            
            if ($quantity <= 0) {
                // Remove from cart if quantity is zero or negative
                unset($_SESSION['cart'][$productId]);
                $message = 'Product removed from cart';
            } else {
                // Update quantity
                $_SESSION['cart'][$productId] = $quantity;
                $message = 'Cart updated';
            }
            
            // Log the action
            if ($this->container->has('logger')) {
                $this->container->get('logger')->info('Cart updated', [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'user_id' => $_SESSION['user_id'] ?? 'guest'
                ]);
            }
            
            return $this->json($response, [
                'success' => true,
                'message' => $message,
                'cart_count' => isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0
            ]);
        } catch (\Exception $e) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error updating cart: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while updating cart'
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
     * Get featured products for home page
     *
     * @return array
     */
    private function getFeaturedProducts(): array
    {
        // In a real app, this would come from a database
        $products = $this->getAllProducts();
        
        return array_filter($products, function($product) {
            return isset($product['featured']) && $product['featured'];
        });
    }
    
    /**
     * Get all products
     *
     * @return array
     */
    private function getAllProducts(): array
    {
        // In a real app, this would come from a database
        return [
            [
                'id' => 1,
                'name' => 'E-commerce Template',
                'slug' => 'ecommerce-template',
                'description' => 'Complete e-commerce solution with payment integration',
                'full_description' => 'Our professional e-commerce template includes product catalog, shopping cart, secure checkout, payment gateway integration, inventory management, and customer account management. Perfect for any online store.',
                'price' => 199.99,
                'sale_price' => 149.99,
                'on_sale' => true,
                'image' => 'ecommerce.jpg',
                'category' => 'business',
                'featured' => true,
                'popular' => true,
                'rating' => 4.8,
                'reviews_count' => 45
            ],
            [
                'id' => 2,
                'name' => 'Blog Template',
                'slug' => 'blog-template',
                'description' => 'Professional blog template with multiple layouts',
                'full_description' => 'A beautiful and responsive blog template with multiple layout options, category management, tag support, commenting system, and social media integration. Ideal for bloggers, writers, and content creators.',
                'price' => 99.99,
                'sale_price' => null,
                'on_sale' => false,
                'image' => 'blog.jpg',
                'category' => 'blog',
                'featured' => false,
                'popular' => true,
                'rating' => 4.5,
                'reviews_count' => 32
            ],
            [
                'id' => 3,
                'name' => 'Portfolio Template',
                'slug' => 'portfolio-template',
                'description' => 'Showcase your work with this premium portfolio template',
                'full_description' => 'This premium portfolio template allows you to showcase your projects, skills, and experience in a visually appealing way. Includes project gallery, filterable categories, about section, contact form, and testimonials.',
                'price' => 89.99,
                'sale_price' => 69.99,
                'on_sale' => true,
                'image' => 'portfolio.jpg',
                'category' => 'portfolio',
                'featured' => true,
                'popular' => false,
                'rating' => 4.7,
                'reviews_count' => 28
            ],
            [
                'id' => 4,
                'name' => 'Responsive Landing Page',
                'slug' => 'responsive-landing-page',
                'description' => 'High-converting landing page template for products or services',
                'full_description' => 'Boost your conversion rates with this professionally designed landing page template. Features include responsive design, call-to-action sections, testimonials, feature showcase, pricing tables, and contact form.',
                'price' => 59.99,
                'sale_price' => null,
                'on_sale' => false,
                'image' => 'landing.jpg',
                'category' => 'marketing',
                'featured' => false,
                'popular' => true,
                'rating' => 4.6,
                'reviews_count' => 37
            ],
            [
                'id' => 5,
                'name' => 'School Management System',
                'slug' => 'school-management-system',
                'description' => 'Complete solution for educational institutions',
                'full_description' => 'A comprehensive school management system for educational institutions. Includes student management, teacher management, course management, attendance tracking, grade management, timetable scheduling, and parent portal.',
                'price' => 299.99,
                'sale_price' => 249.99,
                'on_sale' => true,
                'image' => 'school.jpg',
                'category' => 'education',
                'featured' => true,
                'popular' => false,
                'rating' => 4.9,
                'reviews_count' => 56
            ],
            [
                'id' => 6,
                'name' => 'Real Estate Website',
                'slug' => 'real-estate-website',
                'description' => 'Property listing and management website template',
                'full_description' => 'A feature-rich real estate website template for property listings and management. Includes property search functionality, advanced filtering, property details page, agent profiles, and inquiry forms.',
                'price' => 199.99,
                'sale_price' => null,
                'on_sale' => false,
                'image' => 'realestate.jpg',
                'category' => 'business',
                'featured' => false,
                'popular' => true,
                'rating' => 4.7,
                'reviews_count' => 41
            ]
        ];
    }
    
    /**
     * Get product by ID
     *
     * @param int|string $productId
     * @return array|null
     */
    private function getProductById($productId): ?array
    {
        $products = $this->getAllProducts();
        
        foreach ($products as $product) {
            if ($product['id'] == $productId) {
                return $product;
            }
        }
        
        return null;
    }
    
    /**
     * Get related products based on a product ID
     *
     * @param int|string $currentProductId
     * @return array
     */
    private function getRelatedProducts($currentProductId): array
    {
        $currentProduct = $this->getProductById($currentProductId);
        if (!$currentProduct) {
            return [];
        }
        
        $products = $this->getAllProducts();
        $related = [];
        
        // Find products in the same category
        foreach ($products as $product) {
            if ($product['id'] != $currentProductId && $product['category'] === $currentProduct['category']) {
                $related[] = $product;
            }
            
            // Limit to 3 related products
            if (count($related) >= 3) {
                break;
            }
        }
        
        // If not enough related products, add some from different categories
        if (count($related) < 3) {
            foreach ($products as $product) {
                if ($product['id'] != $currentProductId && !in_array($product, $related)) {
                    $related[] = $product;
                }
                
                // Limit to 3 related products
                if (count($related) >= 3) {
                    break;
                }
            }
        }
        
        return $related;
    }
    
    /**
     * Get products by category
     *
     * @param string $category
     * @return array
     */
    private function getProductsByCategory(string $category): array
    {
        $products = $this->getAllProducts();
        
        return array_filter($products, function($product) use ($category) {
            return strtolower($product['category']) === strtolower($category);
        });
    }
    
    /**
     * Get all categories
     *
     * @return array
     */
    private function getCategories(): array
    {
        $products = $this->getAllProducts();
        $categories = [];
        
        foreach ($products as $product) {
            if (!in_array($product['category'], $categories)) {
                $categories[] = $product['category'];
            }
        }
        
        sort($categories);
        
        return array_map(function($category) {
            return [
                'name' => ucfirst($category),
                'slug' => strtolower($category)
            ];
        }, $categories);
    }
}