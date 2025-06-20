<?php
require_once __DIR__ . '/../autoload.php';

use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\CartController;

class ApiRouter {
    private $routes = [];
    private $middleware = [];
    private $protectedPaths = [
        '/api/user',
        '/api/orders',
        '/api/services/repair',
        '/api/services/fintech',
        '/api/dashboard',
        '/api/profile',
        '/api/billing'
    ];

    public function addMiddleware($callback) {
        $this->middleware[] = $callback;
    }

    public function post($path, $handler, $protected = false) {
        $this->routes['POST'][$path] = [
            'handler' => $handler,
            'protected' => $protected
        ];
    }

    public function get($path, $handler, $protected = false) {
        $this->routes['GET'][$path] = [
            'handler' => $handler,
            'protected' => $protected
        ];
    }

    private function isProtectedPath($path) {
        foreach ($this->protectedPaths as $protectedPath) {
            if (strpos($path, $protectedPath) === 0) {
                return true;
            }
        }
        return false;
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Authentication required', 401);
        }
    }

    public function handle() {
        header('Content-Type: application/json');
        
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Run global middleware
        foreach ($this->middleware as $mw) {
            if (!$mw()) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                return;
            }
        }

        try {
            if (!isset($this->routes[$method][$path])) {
                throw new Exception('Route not found', 404);
            }

            $route = $this->routes[$method][$path];
            
            // Check if route requires authentication
            if ($route['protected'] || $this->isProtectedPath($path)) {
                $this->requireAuth();
            }

            $response = $route['handler']();
            echo json_encode($response);

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500
            ]);
        }
    }
}

// Initialize router
$router = new ApiRouter();

// Add authentication middleware
$router->addMiddleware(function() {
    // Allow API documentation endpoints without authentication
    if (strpos($_SERVER['REQUEST_URI'], '/api/docs') === 0) {
        return true;
    }
    
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    return true;
});

// Define routes
$router->post('/api/auth/login', function() {
    $auth = new AuthController();
    return $auth->login();
});

$router->post('/api/auth/register', function() {
    $auth = new AuthController();
    return $auth->register();
});

$router->get('/api/products', function() {
    $products = new ProductController();
    return $products->list();
});

$router->post('/api/cart/add', function() {
    $cart = new CartController();
    return $cart->addItem();
});

// Add repair service endpoints (migrated from Node.js)
$router->get('/api/repair', function() {
    return [
        'status' => 'success',
        'message' => 'Welcome to the repair service API',
        'endpoints' => [
            'status' => '/api/repair/status',
            'submit' => '/api/repair/submit'
        ],
        'version' => '1.0.0'
    ];
});

$router->get('/api/repair/status', function() {
    return [
        'status' => 'success',
        'message' => 'Repair service is online',
        'timestamp' => date('c')
    ];
});

$router->post('/api/repair/submit', function() {
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    
    return [
        'status' => 'success',
        'message' => 'Repair request received successfully',
        'requestId' => 'repair_' . time(),
        'timestamp' => date('c'),
        'data' => $data
    ];
});

// Add root API endpoint
$router->get('/api', function() {
    return [
        'status' => 'success',
        'message' => 'Welcome to the Repair Service API',
        'endpoints' => [
            'repair_status' => '/api/repair/status',
            'repair_submit' => '/api/repair/submit'
        ],
        'version' => '1.0.0'
    ];
});

// Handle the request
$router->handle();
?>