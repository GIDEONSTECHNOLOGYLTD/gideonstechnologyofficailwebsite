<?php
/**
 * Payment Controller
 * 
 * Handles payment processing for customer orders using configured payment gateways
 */

namespace App\Controllers;

use App\Services\Payment\PaymentFactory;
use App\Core\ConfigManager;
use App\Database\Repository\OrderRepository;
use App\Utilities\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentController
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    
    /**
     * @var \App\View\PhpRenderer
     */
    protected $renderer;
    
    /**
     * @var ConfigManager
     */
    protected $configManager;
    
    /**
     * Constructor
     * 
     * @param OrderRepository $orderRepository
     * @param \App\View\PhpRenderer $renderer
     */
    public function __construct(OrderRepository $orderRepository, \App\View\PhpRenderer $renderer)
    {
        $this->orderRepository = $orderRepository;
        $this->renderer = $renderer;
        $this->configManager = ConfigManager::getInstance();
    }

    /**
     * Show payment options for an order
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        // Get order ID from route arguments
        $orderId = $args['id'] ?? null;
        
        if (!$orderId) {
            return $response->withHeader('Location', '/orders')->withStatus(302);
        }
        
        try {
            // Get order details
            $order = $this->orderRepository->findById($orderId);
            
            // Verify order ownership if user is logged in
            if (!$order || (isset($_SESSION['user']) && $order['user_id'] != $_SESSION['user']['id'])) {
                Logger::warning('Invalid order access attempt', [
                    'order_id' => $orderId,
                    'user_id' => $_SESSION['user']['id'] ?? 'guest'
                ]);
                
                return $this->renderer->render($response->withStatus(404), 'errors/404.php', [
                    'message' => 'Order not found'
                ]);
            }
            
            // Get available payment methods
            $gateways = PaymentFactory::getAllGateways();
            $paymentMethods = [];
            
            foreach ($gateways as $key => $gateway) {
                $paymentMethods[] = [
                    'id' => $key,
                    'name' => $gateway->getName()
                ];
            }
            
            return $this->renderer->render($response, 'payments/show.php', [
                'order' => $order,
                'paymentMethods' => $paymentMethods,
                'testMode' => $this->configManager->get('payment_test_mode', true)
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Payment page error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->renderer->render($response->withStatus(500), 'errors/500.php', [
                'message' => 'An error occurred while processing your request'
            ]);
        }
    }

    /**
     * Process payment for an order
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function process(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $orderId = $data['order_id'] ?? null;
        $paymentMethod = $data['payment_method'] ?? null;
        
        if (!$orderId || !$paymentMethod) {
            return $response->withJson([
                'success' => false,
                'message' => 'Missing payment information'
            ])->withStatus(400);
        }
        
        try {
            // Get order details
            $order = $this->orderRepository->findById($orderId);
            
            // Verify order ownership if user is logged in
            if (!$order || (isset($_SESSION['user']) && $order['user_id'] != $_SESSION['user']['id'])) {
                Logger::warning('Invalid payment attempt', [
                    'order_id' => $orderId,
                    'user_id' => $_SESSION['user']['id'] ?? 'guest'
                ]);
                
                return $response->withJson([
                    'success' => false,
                    'message' => 'Invalid order'
                ])->withStatus(403);
            }
            
            // Get payment gateway instance
            $gateway = PaymentFactory::getGateway($paymentMethod);
            
            if (!$gateway) {
                return $response->withJson([
                    'success' => false,
                    'message' => 'Payment method not available'
                ])->withStatus(400);
            }
            
            // Base URLs for callbacks
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . 
                      $_SERVER['HTTP_HOST'];
            
            $successUrl = $baseUrl . '/payment/success?order_id=' . $orderId;
            $cancelUrl = $baseUrl . '/payment/cancel?order_id=' . $orderId;
            
            // Order data for payment processing
            $orderData = [
                'order_id' => $order['order_number'] ?? $orderId,
                'amount' => $order['total'],
                'description' => 'Order #' . ($order['order_number'] ?? $orderId),
                'currency' => $this->configManager->get('currency', 'GBP'),
                'customer_email' => $order['email'] ?? ''
            ];
            
            // Create payment
            $result = $gateway->createPayment($orderData, $successUrl, $cancelUrl);
            
            // Return the result
            return $response->withJson($result);
            
        } catch (\Exception $e) {
            Logger::error('Payment processing error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'payment_method' => $paymentMethod,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response->withJson([
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage()
            ])->withStatus(500);
        }
    }

    /**
     * Handle successful payment callback
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function success(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $orderId = $params['order_id'] ?? null;
        
        if (!$orderId) {
            return $response->withHeader('Location', '/orders')->withStatus(302);
        }
        
        try {
            $order = $this->orderRepository->findById($orderId);
            
            if (!$order) {
                return $this->renderer->render($response->withStatus(404), 'errors/404.php', [
                    'message' => 'Order not found'
                ]);
            }
            
            // Process based on payment method
            // PayPal returns a payment token
            if (isset($params['token'])) {
                $gateway = PaymentFactory::getGateway('paypal');
                $result = $gateway->capturePayment(['token' => $params['token']]);
            }
            // Stripe returns a session_id
            else if (isset($params['session_id'])) {
                $gateway = PaymentFactory::getGateway('stripe');
                $result = $gateway->capturePayment(['session_id' => $params['session_id']]);
            }
            else {
                // Unknown payment method
                Logger::warning('Unknown payment method in success callback', $params);
                $result = ['success' => false, 'message' => 'Unknown payment method'];
            }
            
            if (isset($result['success']) && $result['success']) {
                // Update order status
                $this->orderRepository->updateStatus($orderId, 'paid');
                
                // Record transaction in database
                // In a real implementation, you'd use a TransactionRepository here
                
                // Show receipt
                return $this->renderer->render($response, 'payments/receipt.php', [
                    'order' => $order,
                    'transaction' => $result
                ]);
            } else {
                Logger::error('Payment verification failed', [
                    'order_id' => $orderId,
                    'result' => $result
                ]);
                
                return $this->renderer->render($response, 'payments/failed.php', [
                    'order' => $order,
                    'error' => $result['message'] ?? 'Payment verification failed'
                ]);
            }
            
        } catch (\Exception $e) {
            Logger::error('Payment success callback error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->renderer->render($response->withStatus(500), 'errors/500.php', [
                'message' => 'An error occurred while processing your payment'
            ]);
        }
    }
    
    /**
     * Handle cancelled payment
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function cancel(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $orderId = $params['order_id'] ?? null;
        
        if (!$orderId) {
            return $response->withHeader('Location', '/orders')->withStatus(302);
        }
        
        try {
            $order = $this->orderRepository->findById($orderId);
            
            if (!$order) {
                return $this->renderer->render($response->withStatus(404), 'errors/404.php', [
                    'message' => 'Order not found'
                ]);
            }
            
            Logger::info('Payment cancelled by user', [
                'order_id' => $orderId
            ]);
            
            return $this->renderer->render($response, 'payments/cancelled.php', [
                'order' => $order
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Payment cancel callback error: ' . $e->getMessage());
            
            return $this->renderer->render($response->withStatus(500), 'errors/500.php', [
                'message' => 'An error occurred while processing your request'
            ]);
        }
    }
    
    /**
     * Handle payment gateway webhooks
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function webhook(Request $request, Response $response, array $args): Response
    {
        // Get gateway name from route arguments
        $gatewayName = $args['gateway'] ?? null;
        
        if (!$gatewayName) {
            return $response->withJson([
                'success' => false,
                'message' => 'Invalid webhook endpoint'
            ])->withStatus(400);
        }
        
        try {
            // Get input data
            $payload = (string)$request->getBody();
            $data = json_decode($payload, true) ?? [];
            
            // Add headers to data for signature verification
            $data['_headers'] = $request->getHeaders();
            
            // Get gateway
            $gateway = PaymentFactory::getGateway($gatewayName);
            
            if (!$gateway) {
                Logger::warning('Webhook received for invalid gateway', [
                    'gateway' => $gatewayName
                ]);
                
                return $response->withJson([
                    'success' => false,
                    'message' => 'Invalid payment gateway'
                ])->withStatus(400);
            }
            
            // Process webhook
            $result = $gateway->handleWebhook($data);
            
            // Return appropriate response
            // Note: Most payment gateways expect a 200 response for all webhooks
            return $response->withJson($result['success'] ? [
                'success' => true,
                'message' => 'Webhook processed successfully'
            ] : [
                'success' => false,
                'message' => $result['message'] ?? 'Webhook processing failed'
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Webhook processing error: ' . $e->getMessage(), [
                'gateway' => $gatewayName,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response->withJson([
                'success' => false,
                'message' => 'Webhook processing error'
            ])->withStatus(500);
        }
    }
}
