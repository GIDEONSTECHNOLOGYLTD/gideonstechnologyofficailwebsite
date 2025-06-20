<?php
/**
 * Admin Payment Controller
 * 
 * Manages payment gateway configuration via the admin interface
 */

namespace App\Controllers\Admin;

use App\Core\ConfigManager;
use App\Utilities\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentController
{
    /**
     * @var \App\View\PhpRenderer
     */
    private $renderer;
    
    /**
     * @var ConfigManager
     */
    private $configManager;
    
    /**
     * Constructor
     * 
     * @param \App\View\PhpRenderer $renderer
     */
    public function __construct(\App\View\PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
        $this->configManager = ConfigManager::getInstance();
    }
    
    /**
     * Display payment gateway settings
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        // Get payment gateway configs
        $paymentConfig = [
            'payment_test_mode' => $this->configManager->get('payment_test_mode', true),
            'paypal_enabled' => $this->configManager->get('paypal_enabled', false),
            'stripe_enabled' => $this->configManager->get('stripe_enabled', false),
            'paypal_client_id' => $this->configManager->get('paypal_client_id', ''),
            'paypal_client_secret' => $this->configManager->get('paypal_client_secret', ''),
            'stripe_publishable_key' => $this->configManager->get('stripe_publishable_key', ''),
            'stripe_secret_key' => $this->configManager->get('stripe_secret_key', ''),
            'currency' => $this->configManager->get('currency', 'GBP'),
            'currency_symbol' => $this->configManager->get('currency_symbol', '£')
        ];
        
        // Flash messages
        $messages = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
        unset($_SESSION['flash']);
        
        return $this->renderer->render($response, 'admin/payment/index.php', [
            'page' => 'payment',
            'config' => $paymentConfig,
            'messages' => $messages
        ]);
    }
    
    /**
     * Save payment gateway settings
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function save(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Update payment test mode
        $this->configManager->set('payment_test_mode', 
            isset($data['payment_test_mode']) && $data['payment_test_mode'] === 'on');
            
        // PayPal settings
        $this->configManager->set('paypal_enabled',
            isset($data['paypal_enabled']) && $data['paypal_enabled'] === 'on');
        $this->configManager->set('paypal_client_id', $data['paypal_client_id'] ?? '');
        $this->configManager->set('paypal_client_secret', $data['paypal_client_secret'] ?? '');
        
        // Stripe settings
        $this->configManager->set('stripe_enabled',
            isset($data['stripe_enabled']) && $data['stripe_enabled'] === 'on');
        $this->configManager->set('stripe_publishable_key', $data['stripe_publishable_key'] ?? '');
        $this->configManager->set('stripe_secret_key', $data['stripe_secret_key'] ?? '');
        
        // Currency settings
        $this->configManager->set('currency', $data['currency'] ?? 'GBP');
        $this->configManager->set('currency_symbol', $data['currency_symbol'] ?? '£');
        
        // Log action
        Logger::info('Admin updated payment gateway settings');
        
        // Set flash message
        $_SESSION['flash'] = ['success' => 'Payment gateway settings saved successfully.'];
        
        // Redirect back
        return $response
            ->withHeader('Location', '/admin/payment')
            ->withStatus(302);
    }
    
    /**
     * Test PayPal connection
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function testPayPal(Request $request, Response $response): Response
    {
        $clientId = $this->configManager->get('paypal_client_id', '');
        $clientSecret = $this->configManager->get('paypal_client_secret', '');
        $testMode = $this->configManager->get('payment_test_mode', true);
        
        if (empty($clientId) || empty($clientSecret)) {
            return $response->withJson([
                'success' => false,
                'message' => 'PayPal client ID or secret is missing'
            ]);
        }
        
        try {
            // Use the PayPal SDK to authenticate
            $baseUrl = $testMode 
                ? 'https://api-m.sandbox.paypal.com' 
                : 'https://api-m.paypal.com';
                
            $ch = curl_init($baseUrl . '/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                Logger::info('PayPal connection test successful');
                return $response->withJson([
                    'success' => true,
                    'message' => 'Successfully connected to PayPal API'
                ]);
            } else {
                Logger::error('PayPal connection test failed with HTTP code: ' . $httpCode);
                return $response->withJson([
                    'success' => false,
                    'message' => 'Failed to connect to PayPal API'
                ]);
            }
        } catch (\Exception $e) {
            Logger::error('PayPal connection test error: ' . $e->getMessage());
            return $response->withJson([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Test Stripe connection
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function testStripe(Request $request, Response $response): Response
    {
        $publishableKey = $this->configManager->get('stripe_publishable_key', '');
        $secretKey = $this->configManager->get('stripe_secret_key', '');
        
        if (empty($publishableKey) || empty($secretKey)) {
            return $response->withJson([
                'success' => false,
                'message' => 'Stripe API keys are missing'
            ]);
        }
        
        try {
            // Use the Stripe SDK to authenticate
            $ch = curl_init('https://api.stripe.com/v1/balance');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $secretKey,
                'Stripe-Version: 2020-08-27'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode == 200) {
                Logger::info('Stripe connection test successful');
                return $response->withJson([
                    'success' => true,
                    'message' => 'Successfully connected to Stripe API'
                ]);
            } else {
                Logger::error('Stripe connection test failed with HTTP code: ' . $httpCode);
                return $response->withJson([
                    'success' => false,
                    'message' => 'Failed to connect to Stripe API'
                ]);
            }
        } catch (\Exception $e) {
            Logger::error('Stripe connection test error: ' . $e->getMessage());
            return $response->withJson([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
