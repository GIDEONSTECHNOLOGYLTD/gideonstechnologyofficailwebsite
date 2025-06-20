<?php
/**
 * Paystack Payment Gateway Implementation
 * 
 * Handles payment processing via Paystack API
 */

namespace App\Services\Payment;

use App\Core\ConfigManager;
use App\Utilities\Logger;

class PaystackGateway implements PaymentGatewayInterface
{
    /**
     * @var string Base URL for Paystack API
     */
    private $apiUrl = 'https://api.paystack.co';
    
    /**
     * @var ConfigManager Configuration manager instance
     */
    private $config;
    
    /**
     * @var bool Whether test mode is enabled
     */
    private $testMode;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = ConfigManager::getInstance();
        $this->testMode = $this->config->get('payment_test_mode', false);
    }

    /**
     * Get the gateway name
     * 
     * @return string
     */
    public function getName(): string
    {
        return 'Paystack';
    }

    /**
     * Check if gateway is properly configured
     * 
     * @return bool
     */
    public function isConfigured(): bool
    {
        $secretKey = $this->config->get('paystack_secret_key', '');
        $publicKey = $this->config->get('paystack_public_key', '');
        
        return !empty($secretKey) && !empty($publicKey);
    }

    /**
     * Create a payment for an order
     * 
     * @param array $orderData Order data including amount, currency, etc.
     * @param string $successUrl Success callback URL
     * @param string $cancelUrl Cancel callback URL
     * @return array Response with redirect URL or error
     */
    public function createPayment(array $orderData, string $successUrl, string $cancelUrl): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Paystack gateway not configured'];
        }

        // Get amount in kobo (Paystack requires amount in kobo - 100 kobo = 1 Naira)
        // For other currencies, convert appropriately
        $amount = round($orderData['amount'] * 100);

        try {
            $payload = [
                'email' => $orderData['customer_email'],
                'amount' => $amount,
                'currency' => $orderData['currency'],
                'reference' => 'ORD_' . $orderData['order_id'] . '_' . time(),
                'callback_url' => $successUrl,
                'metadata' => [
                    'order_id' => $orderData['order_id'],
                    'cancel_url' => $cancelUrl
                ]
            ];

            $response = $this->makeApiRequest('/transaction/initialize', 'POST', $payload);

            if (isset($response['status']) && $response['status'] && isset($response['data']['authorization_url'])) {
                return [
                    'success' => true,
                    'redirect_url' => $response['data']['authorization_url'],
                    'payment_id' => $response['data']['reference'],
                    'message' => 'Payment initialized successfully'
                ];
            } else {
                Logger::error('Paystack payment creation failed', [
                    'response' => $response,
                    'order_id' => $orderData['order_id']
                ]);
                
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'Failed to initialize payment'
                ];
            }
        } catch (\Exception $e) {
            Logger::error('Paystack payment exception: ' . $e->getMessage(), [
                'order_id' => $orderData['order_id'],
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Capture/verify a payment
     * 
     * @param array $data Payment data from callback/webhook
     * @return array
     */
    public function capturePayment(array $data): array
    {
        if (!isset($data['reference'])) {
            return ['success' => false, 'message' => 'Missing reference'];
        }

        try {
            $reference = $data['reference'];
            $response = $this->makeApiRequest('/transaction/verify/' . $reference, 'GET');

            if (isset($response['status']) && $response['status'] && 
                isset($response['data']['status']) && $response['data']['status'] === 'success') {
                
                return [
                    'success' => true,
                    'transaction_id' => $reference,
                    'amount' => $response['data']['amount'] / 100, // Convert from kobo to main currency
                    'payment_method' => 'Paystack',
                    'message' => 'Payment verified successfully'
                ];
            } else {
                Logger::warning('Paystack payment verification failed', [
                    'reference' => $reference,
                    'response' => $response
                ]);
                
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'Payment verification failed'
                ];
            }
        } catch (\Exception $e) {
            Logger::error('Paystack verification exception: ' . $e->getMessage(), [
                'reference' => $data['reference'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle webhook notifications from Paystack
     * 
     * @param array $data Webhook payload
     * @return array
     */
    public function handleWebhook(array $data): array
    {
        try {
            // Verify webhook signature
            $headers = $data['_headers'] ?? [];
            $signature = $headers['X-Paystack-Signature'][0] ?? '';
            $payload = file_get_contents('php://input');
            
            if (!$this->verifyWebhookSignature($payload, $signature)) {
                Logger::warning('Invalid Paystack webhook signature', [
                    'signature' => $signature
                ]);
                
                return ['success' => false, 'message' => 'Invalid signature'];
            }
            
            // Process webhook event
            $event = $data['event'] ?? '';
            $reference = $data['data']['reference'] ?? '';
            
            if ($event === 'charge.success') {
                // Extract order ID from reference (ORD_123_timestamp)
                $refParts = explode('_', $reference);
                $orderId = $refParts[1] ?? null;
                
                if ($orderId) {
                    // In a real implementation, you'd use a repository to update the order status
                    // $this->orderRepository->updateStatus($orderId, 'paid');
                    
                    Logger::info('Paystack payment completed via webhook', [
                        'order_id' => $orderId,
                        'reference' => $reference
                    ]);
                    
                    return [
                        'success' => true,
                        'order_id' => $orderId,
                        'transaction_id' => $reference,
                        'message' => 'Payment completed'
                    ];
                }
            }
            
            return [
                'success' => true,
                'message' => 'Webhook received but no action taken',
                'event' => $event
            ];
        } catch (\Exception $e) {
            Logger::error('Paystack webhook exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Webhook processing error'
            ];
        }
    }

    /**
     * Test the connection to Paystack API
     * 
     * @return array Connection status
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'API credentials not configured'];
        }

        try {
            $response = $this->makeApiRequest('/transaction/initialize', 'POST', [
                'email' => 'test@example.com',
                'amount' => 100, // Minimum amount for test transaction
                'currency' => $this->config->get('currency', 'NGN')
            ]);

            // Connection is successful if we get a response with an error about test transaction
            if (isset($response['status']) && $response['status'] === false) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to Paystack API'
                ];
            } else if (isset($response['status']) && $response['status'] === true) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to Paystack API'
                ];
            }
            
            return [
                'success' => false,
                'message' => $response['message'] ?? 'Unable to connect to Paystack API'
            ];
            
        } catch (\Exception $e) {
            Logger::error('Paystack test connection exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Make an API request to Paystack
     * 
     * @param string $endpoint API endpoint
     * @param string $method HTTP method (GET, POST, etc.)
     * @param array $data Request data (for POST, PUT, etc.)
     * @return array Response data
     * @throws \Exception if request fails
     */
    private function makeApiRequest(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $url = $this->apiUrl . $endpoint;
        $secretKey = $this->config->get('paystack_secret_key', '');
        
        $curl = curl_init();
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $secretKey,
                'Cache-Control: no-cache',
                'Content-Type: application/json'
            ],
        ];
        
        if ($method !== 'GET' && !empty($data)) {
            $curlOptions[CURLOPT_POSTFIELDS] = json_encode($data);
        }
        
        curl_setopt_array($curl, $curlOptions);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            throw new \Exception('cURL Error: ' . $err);
        }
        
        $responseData = json_decode($response, true);
        
        if (!$responseData) {
            throw new \Exception('Invalid API response: ' . $response);
        }
        
        return $responseData;
    }

    /**
     * Verify webhook signature
     * 
     * @param string $payload Raw webhook payload
     * @param string $signature Signature from X-Paystack-Signature header
     * @return bool True if signature is valid
     */
    private function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if (empty($signature)) {
            return false;
        }
        
        $secretKey = $this->config->get('paystack_secret_key', '');
        $calculatedSignature = hash_hmac('sha512', $payload, $secretKey);
        
        return hash_equals($calculatedSignature, $signature);
    }
}
