<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\PaymentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Flash\Messages;

/**
 * Checkout Controller
 * 
 * Handles checkout process and payment
 */
class CheckoutController extends Controller
{
    /**
     * @var Cart Cart model
     */
    protected $cartModel;
    
    /**
     * @var Order Order model
     */
    protected $orderModel;
    
    /**
     * @var Product Product model
     */
    protected $productModel;
    
    /**
     * @var PaymentService Payment service
     */
    protected $paymentService;
    
    /**
     * @var \App\Core\Validator Validator
     */
    protected $validator;
    
    /**
     * @var \App\Models\Cart Cart instance
     */
    protected $cart;
    
    /**
     * @var \App\Services\PaymentGateway Payment gateway
     */
    protected $paymentGateway;
    
    /**
     * Constructor
     * 
     * @param Twig|null $view View renderer
     * @param Messages|null $flash Flash messages
     * @param PaymentService|null $paymentService Payment service
     */
    public function __construct(?Twig $view = null, ?Messages $flash = null, ?PaymentService $paymentService = null)
    {
        parent::__construct($view, $flash);
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->paymentService = $paymentService ?? new PaymentService();
        $this->validator = new \App\Core\Validator();
        $this->cart = new \App\Models\Cart();
        $this->paymentGateway = new \App\Services\PaymentGateway();
        // Removed request, response, and args properties as they are passed to each method
    }
    
    /**
     * Helper method to redirect to a specific URL
     *
     * @param Response $response Response object
     * @param string $url URL to redirect to
     * @param int $status HTTP status code
     * @return Response
     */
    protected function respondWithRedirect(Response $response, string $url, int $status = 302): Response
    {
        return $response->withHeader('Location', $url)
                        ->withStatus($status);
    }

    public function show(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $userId = $user ? $user->id : null;
        
        if (!$userId) {
            $this->flash->addMessage('error', 'You need to be logged in to access checkout');
            return $this->respondWithRedirect($response, '/login');
        }
        
        // Get cart items
        $cartItems = $this->cartModel->findAllByUser($userId);
        
        // Calculate totals
        $subtotal = $this->cartModel->calculateSubtotal();
        $tax = $this->cartModel->calculateTax(0.1); // 10% tax rate
        $shipping = $this->cartModel->calculateShipping('standard');
        $total = $subtotal + $tax + $shipping;
        
        // Check if cart is empty
        if (empty($cartItems)) {
            $this->flash->addMessage('warning', 'Your cart is empty.');
            return $this->respondWithRedirect($response, '/store/cart');
        }
        
        // Fixed: Pass the response, template name, and data array to the view renderer
        return $this->view->render(
            $response, 
            'store/checkout.twig', 
            [
                'cart_items' => $cartItems,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'user' => $user
            ]
        );
    }
    
    public function process(Request $request, Response $response, array $args): Response
    {
        // Get the form data
        $data = $request->getParsedBody();
        
        // Validate the checkout data
        $validation = $this->validator->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'address' => 'required|min:5',
            'payment_method' => 'required'
        ]);
        
        if ($validation->fails()) {
            $this->flash->addMessage('error', 'Please fix the errors in the form');
            return $this->respondWithRedirect($response, '/checkout');
        }
        
        // Get the cart items
        $cart = $this->cart->getItems();
        
        if (empty($cart)) {
            $this->flash->addMessage('error', 'Your cart is empty');
            return $this->respondWithRedirect($response, '/checkout');
        }
        
        // Process the payment using the payment gateway
        $success = $this->paymentGateway->processPayment($data, $cart);
        
        if ($success) {
            // Get user ID
            $userId = $this->auth->user() ? $this->auth->user()->id : null;
            
            // Create the order
            $this->orderModel->create($data, $cart, $userId);
            
            // Clear the cart
            $this->cart->clear();
            
            $this->flash->addMessage('success', 'Your order has been processed successfully');
            return $this->respondWithRedirect($response, '/checkout/success');
        } else {
            $this->flash->addMessage('error', 'There was a problem processing your payment');
            return $this->respondWithRedirect($response, '/checkout/cancel');
        }
    }
    
    public function success(Request $request, Response $response): Response
    {
        // Get order ID from session
        $orderId = $_SESSION['completed_order_id'] ?? null;
        
        if (!$orderId) {
            $this->flash->addMessage('warning', 'No order found');
            return $this->respondWithRedirect($response, '/store');
        }
        
        // Get order details
        $order = $this->orderModel->findById($orderId);
        $orderItems = $this->orderModel->findOrderItems($orderId);
        
        // Clear session variable
        unset($_SESSION['completed_order_id']);
        
        return $this->view->render($response, 'store/checkout_success.twig', [
            'order' => $order,
            'order_items' => $orderItems
        ]);
    }
    
    public function cancel(Request $request, Response $response): Response
    {
        $this->flash->addMessage('info', 'Your checkout has been cancelled.');
        return $this->respondWithRedirect($response, '/store/cart');
    }
    
    protected function processPayment(array $data, float $amount, int $orderId): array
    {
        $paymentMethod = $data['payment_method'];
        
        switch ($paymentMethod) {
            case 'credit_card':
                return $this->paymentService->processCardPayment([
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'card_number' => $data['card_number'],
                    'card_exp_month' => $data['card_exp_month'],
                    'card_exp_year' => $data['card_exp_year'],
                    'card_cvv' => $data['card_cvv']
                ]);
                
            case 'paypal':
                return $this->paymentService->processPayPalPayment([
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'return_url' => getenv('APP_URL') . '/store/checkout/success',
                    'cancel_url' => getenv('APP_URL') . '/store/checkout/cancel'
                ]);
                
            default:
                return [
                    'success' => false,
                    'message' => 'Invalid payment method'
                ];
        }
    }
    
    protected function validateCheckoutData(array $data): array
    {
        $errors = [];
        
        // Validate shipping address
        if (empty($data['shipping_address'])) {
            $errors['shipping_address'] = 'Shipping address is required.';
        }
        
        if (empty($data['shipping_city'])) {
            $errors['shipping_city'] = 'City is required.';
        }
        
        if (empty($data['shipping_state'])) {
            $errors['shipping_state'] = 'State is required.';
        }
        
        if (empty($data['shipping_zip'])) {
            $errors['shipping_zip'] = 'ZIP code is required.';
        }
        
        if (empty($data['shipping_country'])) {
            $errors['shipping_country'] = 'Country is required.';
        }
        
        // Validate billing address if different from shipping
        if (!isset($data['same_as_shipping']) || $data['same_as_shipping'] === '0') {
            if (empty($data['billing_address'])) {
                $errors['billing_address'] = 'Billing address is required.';
            }
            
            if (empty($data['billing_city'])) {
                $errors['billing_city'] = 'City is required.';
            }
            
            if (empty($data['billing_state'])) {
                $errors['billing_state'] = 'State is required.';
            }
            
            if (empty($data['billing_zip'])) {
                $errors['billing_zip'] = 'ZIP code is required.';
            }
            
            if (empty($data['billing_country'])) {
                $errors['billing_country'] = 'Country is required.';
            }
        }
        
        // Validate payment method
        if (empty($data['payment_method'])) {
            $errors['payment_method'] = 'Payment method is required.';
        }
        
        // Validate credit card details if that's the payment method
        if ($data['payment_method'] === 'credit_card') {
            if (empty($data['card_number'])) {
                $errors['card_number'] = 'Card number is required.';
            } elseif (!preg_match('/^\d{13,19}$/', preg_replace('/\s+/', '', $data['card_number']))) {
                $errors['card_number'] = 'Card number is invalid.';
            }
            
            if (empty($data['card_exp_month'])) {
                $errors['card_exp_month'] = 'Expiration month is required.';
            }
            
            if (empty($data['card_exp_year'])) {
                $errors['card_exp_year'] = 'Expiration year is required.';
            }
            
            if (empty($data['card_cvv'])) {
                $errors['card_cvv'] = 'CVV is required.';
            } elseif (!preg_match('/^\d{3,4}$/', $data['card_cvv'])) {
                $errors['card_cvv'] = 'CVV is invalid.';
            }
        }
        
        return $errors;
    }

    /**
     * Display checkout form 
     * 
     * @param Request $request Request object
     * @param Response $response Response object
     * @param array $args Route arguments
     * @return Response
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        // Get current user
        $user = $this->auth->user();
        
        // Check if user is logged in
        if (!$user) {
            $this->flash->addMessage('error', 'You need to be logged in to checkout');
            return $this->respondWithRedirect($response, '/login');
        }
        
        // Get cart items
        $cartItems = $this->cart->getItems();
        
        // Calculate totals
        $subtotal = $this->cart->calculateSubtotal();
        $tax = $this->cart->calculateTax(0.1);
        $shipping = $this->cart->calculateShipping('standard');
        $total = $subtotal + $tax + $shipping;
        
        // Render checkout template
        return $this->view->render($response, 'checkout/index.twig', [
            'cart_items' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'user' => $user
        ]);
    }

    /**
     * Display the checkout page
     * 
     * @param Request $request Request object
     * @param Response $response Response object
     * @param array $args Route arguments
     * @return Response
     */
    public function checkout(Request $request, Response $response, array $args): Response
    {
        // Get current user
        $user = $this->auth->user();
        
        // Check if user is logged in
        if (!$user) {
            $this->flash->addMessage('error', 'You need to be logged in to checkout');
            return $this->respondWithRedirect($response, '/login');
        }
        
        // Get cart items
        $cartItems = $this->cart->getItems();
        
        // Calculate totals
        $subtotal = $this->cart->calculateSubtotal();
        $tax = $this->cart->calculateTax(0.1); // 10% tax rate
        $shipping = $this->cart->calculateShipping('standard');
        $total = $subtotal + $tax + $shipping;
        
        // Check if cart is empty
        if (empty($cartItems)) {
            $this->flash->addMessage('warning', 'Your cart is empty');
            return $this->respondWithRedirect($response, '/cart');
        }
        
        // Render checkout template
        return $this->view->render($response, 'checkout/checkout.twig', [
            'cart_items' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'user' => $user
        ]);
    }
}