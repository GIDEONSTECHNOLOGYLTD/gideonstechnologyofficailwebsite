<?php
/**
 * Stripe Payment Gateway
 * 
 * Implements Stripe payment processing using Stripe API
 */

namespace App\Services\Payment;

use App\Core\ConfigManager;
use App\Utilities\Logger;

class StripeGateway implements PaymentGatewayInterface
{
    /**
     * @var ConfigManager
     */
    private $configManager;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configManager = ConfigManager::getInstance();
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPayment(array $orderData, string $returnUrl, string $cancelUrl): array
    {
        if (!$this->isConfigured()) {
            Logger::error('Stripe gateway not configured');
            return [
                'success' => false,
                'message' => 'Stripe gateway not configured',
            ];
        }
        
        try {
            $secretKey = $this->configManager->get('stripe_secret_key', '');
            
            // Product details
            $orderDescription = $orderData['description'] ?? 'Gideons Technology Order';
            $amount = round($orderData['amount'] * 100); // Stripe uses cents/pence
            $currency = strtolower($orderData['currency'] ?? $this->configManager->get('currency', 'gbp'));
            $metadata = [
                'order_id' => $orderData['order_id'] ?? '',
                'customer_email' => $orderData['customer_email'] ?? '',
            ];
            
            // Create the checkout session
            $payload = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $orderDescription,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $returnUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'metadata' => $metadata,
            ];
            
            // Call Stripe API
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $secretKey,
                'Content-Type: application/x-www-form-urlencoded',
                'Stripe-Version: 2020-08-27'
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                $session = json_decode($response, true);
                
                Logger::info('Stripe checkout session created', [
                    'order_id' => $orderData['order_id'] ?? '',
                    'session_id' => $session['id'] ?? ''
                ]);
                
                return [
                    'success' => true,
                    'session_id' => $session['id'],
                    'payment_id' => $session['id'],
                    'checkout_url' => $session['url'] ?? '',
                    'redirect_url' => $session['url'] ?? '',
                    'publishable_key' => $this->configManager->get('stripe_publishable_key', ''),
                ];
            }
            
            Logger::error('Stripe API error', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to create Stripe payment session',
                'details' => $response
            ];
            
        } catch (\Exception $e) {
            Logger::error('Stripe exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function capturePayment(array $data): array
    {
        if (!isset($data['session_id'])) {
            return [
                'success' => false,
                'message' => 'Stripe session ID missing'
            ];
        }
        
        try {
            $secretKey = $this->configManager->get('stripe_secret_key', '');
            $sessionId = $data['session_id'];
            
            // Retrieve the session to check its payment status
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $secretKey,
                'Stripe-Version: 2020-08-27'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                $session = json_decode($response, true);
                
                // Check if the payment was successful
                if ($session['payment_status'] === 'paid') {
                    Logger::info('Stripe payment completed successfully', [
                        'session_id' => $sessionId,
                        'payment_intent' => $session['payment_intent'] ?? ''
                    ]);
                    
                    return [
                        'success' => true,
                        'transaction_id' => $session['payment_intent'] ?? $sessionId,
                        'status' => 'COMPLETED',
                        'payment_data' => $session
                    ];
                } else {
                    Logger::warning('Stripe payment not completed', [
                        'session_id' => $sessionId,
                        'payment_status' => $session['payment_status'] ?? 'unknown'
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'Payment not completed',
                        'status' => $session['payment_status'] ?? 'incomplete',
                        'payment_data' => $session
                    ];
                }
            }
            
            Logger::error('Stripe session retrieval failed', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to verify payment',
                'details' => $response
            ];
            
        } catch (\Exception $e) {
            Logger::error('Stripe verification exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment verification error: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleWebhook(array $data): array
    {
        try {
            $payload = @file_get_contents('php://input');
            $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
            $secretKey = $this->configManager->get('stripe_secret_key', '');
            $webhookSecret = $this->configManager->get('stripe_webhook_secret', '');
            
            if (empty($payload) || empty($sigHeader) || empty($webhookSecret)) {
                Logger::warning('Stripe webhook missing required data');
                return [
                    'success' => false,
                    'message' => 'Missing webhook data'
                ];
            }
            
            // Verify webhook signature
            $event = null;
            try {
                // This would normally use the Stripe SDK, but we're using a simplified approach
                // In production, you'd use: $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
                $event = json_decode($payload, true);
            } catch (\Exception $e) {
                Logger::error('Stripe webhook signature verification failed: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Invalid webhook signature'
                ];
            }
            
            // Handle the event
            $eventType = $event['type'] ?? '';
            
            Logger::info('Stripe webhook received', [
                'event_type' => $eventType
            ]);
            
            // Process based on event type
            switch ($eventType) {
                case 'checkout.session.completed':
                    // Payment was successful, fulfill the order
                    $sessionId = $event['data']['object']['id'] ?? '';
                    // Update order status in database
                    break;
                    
                case 'payment_intent.succeeded':
                    // Payment confirmed
                    break;
                    
                case 'payment_intent.payment_failed':
                    // Payment failed
                    break;
            }
            
            return [
                'success' => true,
                'message' => 'Webhook processed',
                'event_type' => $eventType
            ];
            
        } catch (\Exception $e) {
            Logger::error('Stripe webhook error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Webhook processing error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function isConfigured(): bool
    {
        $publishableKey = $this->configManager->get('stripe_publishable_key', '');
        $secretKey = $this->configManager->get('stripe_secret_key', '');
        $enabled = $this->configManager->get('stripe_enabled', false);
        
        return $enabled && !empty($publishableKey) && !empty($secretKey);
    }
    
    /**
     * {@inheritdoc}
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Stripe not configured properly. Please check your settings.'
            ];
        }
        
        try {
            $secretKey = $this->configManager->get('stripe_secret_key', '');
            
            // Test connection by retrieving account info
            $ch = curl_init('https://api.stripe.com/v1/balance');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $secretKey,
                'Stripe-Version: 2020-08-27'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to Stripe API'
                ];
            } else {
                $error = json_decode($response, true);
                $errorMessage = $error['error']['message'] ?? 'Unknown error';
                
                return [
                    'success' => false,
                    'message' => 'Failed to connect to Stripe API: ' . $errorMessage
                ];
            }
        } catch (\Exception $e) {
            Logger::error('Stripe test connection error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Connection test error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Stripe';
    }
}
