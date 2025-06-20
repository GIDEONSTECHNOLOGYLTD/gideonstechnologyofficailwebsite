<?php
/**
 * Payment Factory
 * 
 * Creates and returns appropriate payment gateway based on configuration
 */

namespace App\Services\Payment;

use App\Core\ConfigManager;
use App\Utilities\Logger;

class PaymentFactory
{
    /**
     * Get payment gateway instance by name
     * 
     * @param string $gateway Gateway name (paypal, stripe)
     * @return PaymentGatewayInterface|null Gateway instance or null if not available
     */
    public static function getGateway(string $gateway = ''): ?PaymentGatewayInterface
    {
        // If no specific gateway requested, get default from config
        if (empty($gateway)) {
            $configManager = ConfigManager::getInstance();
            
            // First check if Paystack is enabled (primary gateway)
            if ($configManager->get('paystack_enabled', false)) {
                $gateway = 'paystack';
            } 
            // Then check if PayPal is enabled
            else if ($configManager->get('paypal_enabled', false)) {
                $gateway = 'paypal';
            } 
            // Then check if Stripe is enabled
            else if ($configManager->get('stripe_enabled', false)) {
                $gateway = 'stripe';
            } 
            // If none is enabled, log warning and return null
            else {
                Logger::warning('No payment gateway is enabled in configuration');
                return null;
            }
        }
        
        // Normalize gateway name
        $gateway = strtolower($gateway);
        
        // Create and return requested gateway
        switch ($gateway) {
            case 'paystack':
                return new PaystackGateway();
                
            case 'paypal':
                return new PayPalGateway();
                
            case 'stripe':
                return new StripeGateway();
                
            default:
                Logger::error('Unknown payment gateway: ' . $gateway);
                return null;
        }
    }
    
    /**
     * Get all configured and enabled payment gateways
     * 
     * @return array Array of payment gateway instances
     */
    public static function getAllGateways(): array
    {
        $configManager = ConfigManager::getInstance();
        $gateways = [];
        
        // Add Paystack if enabled (primary gateway)
        if ($configManager->get('paystack_enabled', true)) { // Default to enabled for Paystack
            $paystack = new PaystackGateway();
            if ($paystack->isConfigured()) {
                $gateways['paystack'] = $paystack;
            }
        }
        
        // Add PayPal if enabled
        if ($configManager->get('paypal_enabled', false)) {
            $paypal = new PayPalGateway();
            if ($paypal->isConfigured()) {
                $gateways['paypal'] = $paypal;
            }
        }
        
        // Add Stripe if enabled
        if ($configManager->get('stripe_enabled', false)) {
            $stripe = new StripeGateway();
            if ($stripe->isConfigured()) {
                $gateways['stripe'] = $stripe;
            }
        }
        
        return $gateways;
    }
}
