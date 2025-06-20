<?php
/**
 * Admin Payment Configuration Controller
 * 
 * Manages payment gateway configurations in the admin panel
 */

namespace App\Controllers\Admin;

use App\Core\ConfigManager;
use App\Services\Payment\PaymentFactory;
use App\Services\Mailer\GmailMailer;
use App\Utilities\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentConfigController
{
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
     * @param \App\View\PhpRenderer $renderer
     */
    public function __construct(\App\View\PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
        $this->configManager = ConfigManager::getInstance();
    }

    /**
     * Show payment configuration page
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $paystackEnabled = $this->configManager->get('paystack_enabled', true);
        $paypalEnabled = $this->configManager->get('paypal_enabled', false);
        $stripeEnabled = $this->configManager->get('stripe_enabled', false);
        $testMode = $this->configManager->get('payment_test_mode', true);
        $currency = $this->configManager->get('currency', 'NGN');
        
        return $this->renderer->render($response, 'admin/payment_config.php', [
            'paystack_enabled' => $paystackEnabled,
            'paystack_public_key' => $this->configManager->get('paystack_public_key', ''),
            'paystack_secret_key' => $this->configManager->get('paystack_secret_key', ''),
            'paypal_enabled' => $paypalEnabled,
            'paypal_client_id' => $this->configManager->get('paypal_client_id', ''),
            'paypal_client_secret' => $this->configManager->get('paypal_client_secret', ''),
            'stripe_enabled' => $stripeEnabled,
            'stripe_publishable_key' => $this->configManager->get('stripe_publishable_key', ''),
            'stripe_secret_key' => $this->configManager->get('stripe_secret_key', ''),
            'payment_test_mode' => $testMode,
            'currency' => $currency,
            'currencies' => ['NGN', 'USD', 'GBP', 'EUR'],
            'activeTab' => 'payment'
        ]);
    }

    /**
     * Update payment configuration
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate required fields
        if (!isset($data['payment_gateway']) || empty($data['payment_gateway'])) {
            return $response->withJson([
                'success' => false,
                'message' => 'You must select at least one payment gateway'
            ])->withStatus(400);
        }
        
        // Update configuration
        try {
            // General payment settings
            $this->configManager->set('payment_test_mode', isset($data['payment_test_mode']) && $data['payment_test_mode'] === 'on');
            $this->configManager->set('currency', $data['currency'] ?? 'NGN');
            
            // Gateway status
            $this->configManager->set('paystack_enabled', in_array('paystack', $data['payment_gateway'] ?? []));
            $this->configManager->set('paypal_enabled', in_array('paypal', $data['payment_gateway'] ?? []));
            $this->configManager->set('stripe_enabled', in_array('stripe', $data['payment_gateway'] ?? []));
            
            // Paystack Settings
            if (isset($data['paystack_public_key'])) {
                $this->configManager->set('paystack_public_key', $data['paystack_public_key']);
            }
            if (isset($data['paystack_secret_key'])) {
                $this->configManager->set('paystack_secret_key', $data['paystack_secret_key']);
            }
            
            // PayPal Settings
            if (isset($data['paypal_client_id'])) {
                $this->configManager->set('paypal_client_id', $data['paypal_client_id']);
            }
            if (isset($data['paypal_client_secret'])) {
                $this->configManager->set('paypal_client_secret', $data['paypal_client_secret']);
            }
            
            // Stripe Settings
            if (isset($data['stripe_publishable_key'])) {
                $this->configManager->set('stripe_publishable_key', $data['stripe_publishable_key']);
            }
            if (isset($data['stripe_secret_key'])) {
                $this->configManager->set('stripe_secret_key', $data['stripe_secret_key']);
            }
            
            // Save all changes
            $this->configManager->save();
            
            return $response->withJson([
                'success' => true,
                'message' => 'Payment configuration updated successfully'
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Error updating payment configuration: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response->withJson([
                'success' => false,
                'message' => 'Failed to update payment configuration'
            ])->withStatus(500);
        }
    }

    /**
     * Test payment gateway connection
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function testGateway(Request $request, Response $response, array $args): Response
    {
        $gatewayName = $args['gateway'] ?? '';
        
        if (empty($gatewayName)) {
            return $response->withJson([
                'success' => false,
                'message' => 'Gateway not specified'
            ])->withStatus(400);
        }
        
        try {
            // Get gateway instance
            $gateway = PaymentFactory::getGateway($gatewayName);
            
            if (!$gateway) {
                return $response->withJson([
                    'success' => false,
                    'message' => 'Gateway not available'
                ])->withStatus(400);
            }
            
            // Test the connection
            $result = $gateway->testConnection();
            
            return $response->withJson($result);
            
        } catch (\Exception $e) {
            Logger::error('Payment gateway test error: ' . $e->getMessage(), [
                'gateway' => $gatewayName,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response->withJson([
                'success' => false,
                'message' => 'Gateway test failed: ' . $e->getMessage()
            ])->withStatus(500);
        }
    }

    /**
     * Email configuration page
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function emailConfig(Request $request, Response $response): Response
    {
        return $this->renderer->render($response, 'admin/email_config.php', [
            'gmail_username' => $this->configManager->get('gmail_username', ''),
            'gmail_app_password' => $this->configManager->get('gmail_app_password', ''),
            'mail_from_address' => $this->configManager->get('mail_from_address', ''),
            'mail_from_name' => $this->configManager->get('mail_from_name', 'Gideons Technology Ltd'),
            'admin_email' => $this->configManager->get('admin_email', ''),
            'activeTab' => 'email'
        ]);
    }

    /**
     * Update email configuration
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function updateEmailConfig(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate required fields
        if (!isset($data['gmail_username']) || empty($data['gmail_username'])) {
            return $response->withJson([
                'success' => false,
                'message' => 'Gmail username is required'
            ])->withStatus(400);
        }
        
        // Update configuration
        try {
            $this->configManager->set('gmail_username', $data['gmail_username']);
            
            // Only update password if provided
            if (isset($data['gmail_app_password']) && !empty($data['gmail_app_password'])) {
                $this->configManager->set('gmail_app_password', $data['gmail_app_password']);
            }
            
            $this->configManager->set('mail_from_address', $data['mail_from_address'] ?? $data['gmail_username']);
            $this->configManager->set('mail_from_name', $data['mail_from_name'] ?? 'Gideons Technology Ltd');
            $this->configManager->set('admin_email', $data['admin_email'] ?? $data['gmail_username']);
            
            // Save all changes
            $this->configManager->save();
            
            return $response->withJson([
                'success' => true,
                'message' => 'Email configuration updated successfully'
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Error updating email configuration: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response->withJson([
                'success' => false,
                'message' => 'Failed to update email configuration'
            ])->withStatus(500);
        }
    }

    /**
     * Test email configuration
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function testEmail(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $testEmail = $data['test_email'] ?? '';
        
        try {
            $mailer = new GmailMailer();
            $result = $mailer->testConnection($testEmail);
            
            return $response->withJson($result);
            
        } catch (\Exception $e) {
            Logger::error('Email test error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $response->withJson([
                'success' => false,
                'message' => 'Email test failed: ' . $e->getMessage()
            ])->withStatus(500);
        }
    }
}
