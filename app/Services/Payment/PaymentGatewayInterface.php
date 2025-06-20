<?php
/**
 * Payment Gateway Interface
 * 
 * Defines the contract that all payment gateway implementations must follow
 */

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Generate a payment URL or form for checkout
     * 
     * @param array $orderData Order information (amount, items, etc)
     * @param string $returnUrl URL to redirect after successful payment
     * @param string $cancelUrl URL to redirect after cancelled payment
     * @return array Response with status and redirect URL or form HTML
     */
    public function createPayment(array $orderData, string $returnUrl, string $cancelUrl): array;
    
    /**
     * Verify and capture payment
     * 
     * @param array $data Payment data received from gateway
     * @return array Payment verification result
     */
    public function capturePayment(array $data): array;
    
    /**
     * Process webhook notification from payment gateway
     * 
     * @param array $data Webhook data
     * @return array Processing result
     */
    public function handleWebhook(array $data): array;
    
    /**
     * Check if gateway is properly configured
     * 
     * @return bool True if configured
     */
    public function isConfigured(): bool;
    
    /**
     * Test connection to the payment gateway API
     * 
     * @return array Test result with success status and message
     */
    public function testConnection(): array;
    
    /**
     * Get gateway display name
     * 
     * @return string Display name
     */
    public function getName(): string;
}
