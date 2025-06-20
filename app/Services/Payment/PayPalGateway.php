<?php
/**
 * PayPal Payment Gateway
 * 
 * Implements PayPal payment processing using PayPal REST API
 */

namespace App\Services\Payment;

use App\Core\ConfigManager;
use App\Utilities\Logger;

class PayPalGateway implements PaymentGatewayInterface
{
    /**
     * @var ConfigManager
     */
    private $configManager;
    
    /**
     * @var string API Base URL
     */
    private $apiBase;
    
    /**
     * @var string Access token
     */
    private $accessToken = null;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configManager = ConfigManager::getInstance();
        $testMode = $this->configManager->get('payment_test_mode', true);
        
        // Set API base URL based on mode
        $this->apiBase = $testMode 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com';
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPayment(array $orderData, string $returnUrl, string $cancelUrl): array
    {
        if (!$this->isConfigured()) {
            Logger::error('PayPal gateway not configured');
            return [
                'success' => false,
                'message' => 'PayPal gateway not configured',
            ];
        }
        
        try {
            // Get access token
            $token = $this->getAccessToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPal',
                ];
            }
            
            // Prepare order data
            $payload = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $orderData['order_id'] ?? uniqid('order-'),
                        'description' => $orderData['description'] ?? 'Gideons Technology Order',
                        'amount' => [
                            'currency_code' => $orderData['currency'] ?? $this->configManager->get('currency', 'GBP'),
                            'value' => number_format($orderData['amount'], 2, '.', ''),
                        ]
                    ],
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                    'brand_name' => $this->configManager->get('site_name', 'GIDEONS TECHNOLOGY LTD'),
                    'user_action' => 'PAY_NOW',
                ]
            ];
            
            // Create PayPal order
            $ch = curl_init($this->apiBase . '/v2/checkout/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                $paypalResponse = json_decode($response, true);
                
                // Find approval URL
                $approvalUrl = '';
                foreach ($paypalResponse['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        $approvalUrl = $link['href'];
                        break;
                    }
                }
                
                if ($approvalUrl) {
                    Logger::info('PayPal payment created successfully', [
                        'order_id' => $orderData['order_id'] ?? '',
                        'paypal_id' => $paypalResponse['id'] ?? ''
                    ]);
                    
                    return [
                        'success' => true,
                        'redirect_url' => $approvalUrl,
                        'payment_id' => $paypalResponse['id'],
                        'status' => $paypalResponse['status'] ?? '',
                    ];
                }
            }
            
            Logger::error('PayPal API error', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to create PayPal payment',
                'details' => $response
            ];
            
        } catch (\Exception $e) {
            Logger::error('PayPal exception: ' . $e->getMessage(), [
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
        if (!isset($data['token'])) {
            return [
                'success' => false,
                'message' => 'Payment token missing'
            ];
        }
        
        try {
            // Get access token
            $token = $this->getAccessToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPal',
                ];
            }
            
            $paymentId = $data['token'];
            
            // Capture the payment
            $ch = curl_init($this->apiBase . "/v2/checkout/orders/{$paymentId}/capture");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{}'); // Empty JSON object required
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                $captureData = json_decode($response, true);
                
                Logger::info('PayPal payment captured successfully', [
                    'payment_id' => $paymentId,
                    'status' => $captureData['status'] ?? 'COMPLETED'
                ]);
                
                return [
                    'success' => true,
                    'transaction_id' => $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? $paymentId,
                    'status' => $captureData['status'] ?? 'COMPLETED',
                    'payment_data' => $captureData
                ];
            }
            
            Logger::error('PayPal capture failed', [
                'http_code' => $httpCode,
                'response' => $response,
                'payment_id' => $paymentId
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment capture failed',
                'details' => $response
            ];
            
        } catch (\Exception $e) {
            Logger::error('PayPal capture exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment capture error: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleWebhook(array $data): array
    {
        // Process webhook notifications from PayPal
        // This is a simplified implementation
        try {
            Logger::info('PayPal webhook received', [
                'event_type' => $data['event_type'] ?? 'unknown'
            ]);
            
            // Verify webhook signature (should be implemented for production)
            // Process based on event type
            
            return [
                'success' => true,
                'message' => 'Webhook processed',
                'event_type' => $data['event_type'] ?? 'unknown'
            ];
            
        } catch (\Exception $e) {
            Logger::error('PayPal webhook error: ' . $e->getMessage());
            
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
        $clientId = $this->configManager->get('paypal_client_id', '');
        $clientSecret = $this->configManager->get('paypal_client_secret', '');
        $enabled = $this->configManager->get('paypal_enabled', false);
        
        return $enabled && !empty($clientId) && !empty($clientSecret);
    }
    
    /**
     * {@inheritdoc}
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'PayPal not configured properly. Please check your settings.'
            ];
        }
        
        try {
            // Attempt to get an access token as connection test
            $token = $this->getAccessToken(true); // Force new token
            
            if ($token) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to PayPal API'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPal API'
                ];
            }
        } catch (\Exception $e) {
            Logger::error('PayPal test connection error: ' . $e->getMessage());
            
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
        return 'PayPal';
    }
    
    /**
     * Get access token from PayPal API
     * 
     * @param bool $forceNew Force getting a new token
     * @return string|null Access token or null on failure
     */
    private function getAccessToken(bool $forceNew = false): ?string
    {
        // Return cached token if available
        if (!$forceNew && $this->accessToken) {
            return $this->accessToken;
        }
        
        $clientId = $this->configManager->get('paypal_client_id', '');
        $clientSecret = $this->configManager->get('paypal_client_secret', '');
        
        if (empty($clientId) || empty($clientSecret)) {
            Logger::error('PayPal API credentials missing');
            return null;
        }
        
        try {
            $ch = curl_init($this->apiBase . '/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                $data = json_decode($response, true);
                $this->accessToken = $data['access_token'] ?? null;
                return $this->accessToken;
            }
            
            Logger::error('PayPal authentication failed', [
                'http_code' => $httpCode,
                'response' => $response
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Logger::error('PayPal authentication exception: ' . $e->getMessage());
            return null;
        }
    }
}
