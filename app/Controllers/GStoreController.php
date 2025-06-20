<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use App\Core\Auth; // Add the correct Auth namespace
use App\Utilities\Logger;

class GStoreController {
    protected $container;
    private $product;
    private $cart;
    private $order;
    private $user;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->product = new Product();
        $this->cart = new Cart();
        $this->order = new Order();
        // User authentication will be retrieved per request
    }

    private function requireLogin(Response $response) {
        // Check if user is logged in, redirect if not
        if (!isset($_SESSION['user_id'])) {
            Logger::info('Unauthorized access attempt to protected GStore route');
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        return false;
    }

    public function index(Request $request, Response $response): Response {
        Logger::info('GStore index page requested');
        $featuredProducts = $this->product->getFeatured();
        
        return $this->container->get('renderer')->render($response, 'gstore/index.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore',
            'products' => $featuredProducts
        ]);
    }

    public function product(Request $request, Response $response, array $args): Response {
        $productId = $args['id'] ?? 0;
        Logger::info('GStore product page requested for product ID: ' . $productId);
        
        $product = $this->product->getById($productId);
        
        return $this->container->get('renderer')->render($response, 'gstore/product.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore-product',
            'product' => $product
        ]);
    }

    public function category(Request $request, Response $response, array $args): Response {
        $category = $args['category'] ?? 'all';
        Logger::info('GStore category page requested for: ' . $category);
        
        $products = $this->product->getByCategory($category);
        
        return $this->container->get('renderer')->render($response, 'gstore/category.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore-category',
            'category' => $category,
            'products' => $products
        ]);
    }

    public function cart(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        Logger::info('GStore cart page requested');
        
        $userId = $_SESSION['user_id'] ?? 0;
        $cartItems = $this->cart->getItems($userId);
        
        return $this->container->get('renderer')->render($response, 'gstore/cart.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore-cart',
            'items' => $cartItems
        ]);
    }

    public function checkout(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        Logger::info('GStore checkout page requested');
        
        $userId = $_SESSION['user_id'] ?? 0;
        $cartItems = $this->cart->getItems($userId);
        
        return $this->container->get('renderer')->render($response, 'gstore/checkout.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore-checkout',
            'items' => $cartItems
        ]);
    }

    public function orders(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        Logger::info('GStore orders page requested');
        
        $userId = $_SESSION['user_id'] ?? 0;
        $orders = $this->order->getUserOrders($userId);
        
        return $this->container->get('renderer')->render($response, 'gstore/orders.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore-orders',
            'orders' => $orders
        ]);
    }

    public function coupons(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        Logger::info('GStore coupons page requested');
        
        $coupons = $this->order->getAvailableCoupons();
        
        return $this->container->get('renderer')->render($response, 'gstore/coupons.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore-coupons',
            'coupons' => $coupons
        ]);
    }

    public function addToCart(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        $params = $request->getParsedBody();
        $productId = $params['product_id'] ?? 0;
        $quantity = (int)($params['quantity'] ?? 1);
        
        Logger::info('Adding product to cart: ' . $productId . ' (quantity: ' . $quantity . ')');
        
        $userId = $_SESSION['user_id'] ?? 0;
        $this->cart->addItem($userId, $productId, $quantity);
        
        return $response->withHeader('Location', '/gstore/cart')->withStatus(302);
    }

    public function removeFromCart(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        $params = $request->getParsedBody();
        $productId = $params['product_id'] ?? 0;
        
        Logger::info('Removing product from cart: ' . $productId);
        
        $userId = $_SESSION['user_id'] ?? 0;
        $this->cart->removeItem($userId, $productId);
        
        return $response->withHeader('Location', '/gstore/cart')->withStatus(302);
    }

    public function addToWishlist(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        $params = $request->getParsedBody();
        $productId = $params['product_id'] ?? 0;
        
        Logger::info('Adding product to wishlist: ' . $productId);
        
        $userId = $_SESSION['user_id'] ?? 0;
        // Assuming you have a wishlist model or method
        $this->product->addToWishlist($userId, $productId);
        
        return $response->withHeader('Location', '/gstore/wishlist')->withStatus(302);
    }

    public function processCheckout(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        $params = $request->getParsedBody();
        
        Logger::info('Processing checkout');
        
        $userId = $_SESSION['user_id'] ?? 0;
        // Process the order
        $orderId = $this->order->createFromCart($userId, $params);
        
        // Clear cart after successful checkout
        $this->cart->clear($userId);
        
        // Redirect to order success page
        return $response->withHeader('Location', '/gstore/order/success/' . $orderId)->withStatus(302);
    }
    
    public function wishlist(Request $request, Response $response): Response {
        // Check login
        $redirect = $this->requireLogin($response);
        if ($redirect) return $redirect;
        
        Logger::info('GStore wishlist page requested');
        
        $userId = $_SESSION['user_id'] ?? 0;
        $wishlistItems = $this->product->getWishlistItems($userId);
        
        return $this->container->get('renderer')->render($response, 'gstore/wishlist.php', [
            'appName' => $this->container->get('settings')['appName'],
            'currentYear' => $this->container->get('settings')['currentYear'],
            'page' => 'gstore-wishlist',
            'wishlistItems' => $wishlistItems
        ]);
    }
}
