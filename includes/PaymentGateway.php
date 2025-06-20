<?php
/**
 * PaymentGateway - Handles secure payment processing through a third-party provider
 * 
 * This class integrates with Stripe to handle payment processing securely without
 * storing sensitive credit card information on our servers.
 */
class PaymentGateway {
    private $apiKey;
    private $isLive;
    private $gatewayName;
    private $logger;

    /**
     * Constructor
     * 
     * @param string $gatewayName The payment provider to use (default: 'stripe')
     * @param bool $isLive Whether to use live or test mode
     */
    public function __construct($gatewayName = 'stripe', $isLive = false) {
        $this->gatewayName = strtolower($gatewayName);
        $this->isLive = $isLive;
        
        // Load API keys from environment variables
        if ($this->isLive) {
            $this->apiKey = getenv('STRIPE_LIVE_KEY');
        } else {
            $this->apiKey = getenv('STRIPE_TEST_KEY');
        }
        
        // Set up logging
        $this->logger = new Logger('payment');
    }
    
    /**
     * Process a payment
     * 
     * @param array $paymentData Payment information
     * @param float $amount Amount to charge
     * @param string $currency Currency code (default: USD)
     * @return array Result of the payment operation
     */
    public function processPayment($paymentData, $amount, $currency = 'USD') {
        switch ($this->gatewayName) {
            case 'stripe':
                return $this->processStripePayment($paymentData, $amount, $currency);
            case 'paypal':
                return $this->processPayPalPayment($paymentData, $amount, $currency);
            default:
                throw new Exception("Unsupported payment gateway: {$this->gatewayName}");
        }
    }
    
    /**
     * Process a payment through Stripe
     * 
     * @param array $paymentData Payment information
     * @param float $amount Amount to charge
     * @param string $currency Currency code
     * @return array Result of the payment operation
     */
    private function processStripePayment($paymentData, $amount, $currency) {
        if (!class_exists('\\Stripe\\Stripe')) {
            // Check if Stripe library is installed
            if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
                require_once dirname(__DIR__) . '/vendor/autoload.php';
            } else {
                throw new Exception('Stripe library not found. Run "composer require stripe/stripe-php"');
            }
        }
        
        try {
            // Set API key
            \Stripe\Stripe::setApiKey($this->apiKey);
            
            // Convert amount to cents (Stripe requires integer in smallest currency unit)
            $amountInCents = round($amount * 100);
            
            // Create payment intent (modern way, more secure than tokens)
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'payment_method_types' => ['card'],
                'description' => $paymentData['description'] ?? 'Payment for order',
                'metadata' => [
                    'order_id' => $paymentData['order_id'] ?? '',
                    'customer_id' => $paymentData['customer_id'] ?? ''
                ],
                'receipt_email' => $paymentData['email'] ?? null,
            ]);
            
            $this->logger->info('Payment intent created', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $currency,
                'order_id' => $paymentData['order_id'] ?? null
            ]);
            
            return [
                'success' => true,
                'payment_intent' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $amount,
                'currency' => $currency
            ];
        } catch (\Stripe\Exception\CardException $e) {
            // Card was declined
            $this->logger->error('Card payment failed', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'order_id' => $paymentData['order_id'] ?? null
            ]);
            
            return [
                'success' => false,
                'error' => 'Your card was declined: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        } catch (\Exception $e) {
            // Other Stripe errors
            $this->logger->error('Payment processing error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'order_id' => $paymentData['order_id'] ?? null
            ]);
            
            return [
                'success' => false,
                'error' => 'An error occurred while processing your payment: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        }
    }
    
    /**
     * Process a payment through PayPal
     * 
     * @param array $paymentData Payment information
     * @param float $amount Amount to charge
     * @param string $currency Currency code
     * @return array Result of the payment operation
     */
    private function processPayPalPayment($paymentData, $amount, $currency) {
        // Implementation for PayPal integration would go here
        // This is a placeholder for future implementation
        
        $this->logger->info('PayPal payment requested but not implemented yet', [
            'amount' => $amount,
            'currency' => $currency,
            'order_id' => $paymentData['order_id'] ?? null
        ]);
        
        return [
            'success' => false,
            'error' => 'PayPal integration is not implemented yet.'
        ];
    }
    
    /**
     * Create a payment form token for secure client-side processing
     * 
     * @param array $options Additional options
     * @return array Token information for the frontend
     */
    public function createPaymentToken($options = []) {
        if ($this->gatewayName === 'stripe') {
            if (!class_exists('\\Stripe\\Stripe')) {
                if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
                    require_once dirname(__DIR__) . '/vendor/autoload.php';
                } else {
                    throw new Exception('Stripe library not found.');
                }
            }
            
            \Stripe\Stripe::setApiKey($this->apiKey);
            
            // For Stripe, we just need the publishable key for frontend
            return [
                'token_type' => 'publishable_key',
                'token' => $this->isLive ? getenv('STRIPE_LIVE_PUBLISHABLE_KEY') : getenv('STRIPE_TEST_PUBLISHABLE_KEY'),
                'is_live' => $this->isLive
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Unsupported payment gateway for token creation'
        ];
    }
    
    /**
     * Handle payment webhooks from the payment provider
     * 
     * @param string $payload The raw webhook payload
     * @param array $headers HTTP headers from the request
     * @return bool Whether the webhook was handled successfully
     */
    public function handleWebhook($payload, $headers = []) {
        if ($this->gatewayName === 'stripe') {
            return $this->handleStripeWebhook($payload, $headers);
        }
        
        return false;
    }
    
    /**
     * Handle Stripe webhook events
     * 
     * @param string $payload The raw webhook payload
     * @param array $headers HTTP headers from the request
     * @return bool Whether the webhook was handled successfully
     */
    private function handleStripeWebhook($payload, $headers) {
        if (!class_exists('\\Stripe\\Stripe')) {
            if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
                require_once dirname(__DIR__) . '/vendor/autoload.php';
            } else {
                throw new Exception('Stripe library not found.');
            }
        }
        
        $webhookSecret = $this->isLive ? getenv('STRIPE_LIVE_WEBHOOK_SECRET') : getenv('STRIPE_TEST_WEBHOOK_SECRET');
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $headers['Stripe-Signature'] ?? '',
                $webhookSecret
            );
            
            // Handle specific event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $orderId = $paymentIntent->metadata->order_id ?? null;
                    
                    $this->logger->info('Payment succeeded webhook received', [
                        'payment_intent_id' => $paymentIntent->id,
                        'order_id' => $orderId,
                        'amount' => $paymentIntent->amount / 100 // Convert from cents
                    ]);
                    
                    // Update order status in your database
                    if ($orderId) {
                        // Call your order processing service here
                        $orderService = new OrderService();
                        $orderService->updateOrderPaymentStatus($orderId, 'paid');
                    }
                    break;
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $orderId = $paymentIntent->metadata->order_id ?? null;
                    
                    $this->logger->warning('Payment failed webhook received', [
                        'payment_intent_id' => $paymentIntent->id,
                        'order_id' => $orderId,
                        'error' => $paymentIntent->last_payment_error->message ?? 'Unknown error'
                    ]);
                    
                    // Update order status in your database
                    if ($orderId) {
                        $orderService = new OrderService();
                        $orderService->updateOrderPaymentStatus($orderId, 'failed');
                    }
                    break;
                
                // Add other event types as needed
            }
            
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Webhook error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            
            return false;
        }
    }
    
    /**
     * Refund a payment
     * 
     * @param string $paymentId Payment ID to refund
     * @param float $amount Amount to refund (null for full refund)
     * @param string $reason Reason for the refund
     * @return array Refund result
     */
    public function refundPayment($paymentId, $amount = null, $reason = '') {
        if ($this->gatewayName === 'stripe') {
            if (!class_exists('\\Stripe\\Stripe')) {
                if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
                    require_once dirname(__DIR__) . '/vendor/autoload.php';
                } else {
                    throw new Exception('Stripe library not found.');
                }
            }
            
            \Stripe\Stripe::setApiKey($this->apiKey);
            
            try {
                $refundParams = [
                    'payment_intent' => $paymentId,
                    'reason' => empty($reason) ? 'requested_by_customer' : $reason
                ];
                
                if ($amount !== null) {
                    $refundParams['amount'] = round($amount * 100); // Convert to cents
                }
                
                $refund = \Stripe\Refund::create($refundParams);
                
                $this->logger->info('Payment refunded', [
                    'payment_id' => $paymentId,
                    'refund_id' => $refund->id,
                    'amount' => $amount ?? 'full amount'
                ]);
                
                return [
                    'success' => true,
                    'refund_id' => $refund->id,
                    'amount' => $amount ?? 'full'
                ];
            } catch (\Exception $e) {
                $this->logger->error('Refund error', [
                    'error' => $e->getMessage(),
                    'payment_id' => $paymentId
                ]);
                
                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => false,
            'error' => 'Unsupported payment gateway for refund processing'
        ];
    }
}

/**
 * Simple logging class for payment operations
 * In production, you would use a more robust logging solution
 */
class Logger {
    private $channel;
    
    public function __construct($channel) {
        $this->channel = $channel;
    }
    
    public function info($message, $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    public function error($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }
    
    public function warning($message, $context = []) {
        $this->log('WARNING', $message, $context);
    }
    
    private function log($level, $message, $context) {
        $timestamp = date('Y-m-d H:i:s');
        $contextString = json_encode($context);
        $logMessage = "[$timestamp] [$level] [$this->channel] $message $contextString\n";
        
        $logFile = dirname(__DIR__) . '/logs/payments.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}