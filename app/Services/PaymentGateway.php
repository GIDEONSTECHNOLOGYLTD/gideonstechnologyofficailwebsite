<?php

namespace App\Services;

/**
 * Payment Gateway Service
 * 
 * Handles payment processing with various payment providers
 */
class PaymentGateway
{
    /**
     * API keys for payment providers
     * @var array
     */
    protected $keys;
    
    /**
     * Payment gateway configuration
     * @var array
     */
    protected $config;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Load configuration from environment variables
        $this->keys = [
            'stripe' => [
                'secret' => getenv('STRIPE_SECRET_KEY') ?: '',
                'public' => getenv('STRIPE_PUBLIC_KEY') ?: ''
            ],
            'paypal' => [
                'client_id' => getenv('PAYPAL_CLIENT_ID') ?: '',
                'secret' => getenv('PAYPAL_SECRET') ?: ''
            ]
        ];
        
        $this->config = [
            'mode' => getenv('PAYMENT_MODE') ?: 'sandbox', // sandbox or live
            'currency' => getenv('PAYMENT_CURRENCY') ?: 'USD'
        ];
    }
    
    /**
     * Process payment
     * 
     * @param array $data Payment data
     * @param array $cart Cart items
     * @return bool Success status
     */
    public function processPayment(array $data, array $cart): bool
    {
        $paymentMethod = $data['payment_method'] ?? 'credit_card';
        
        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['price'] * $item['quantity']);
        }
        
        // Add tax and shipping if provided
        if (isset($data['tax'])) {
            $total += floatval($data['tax']);
        }
        
        if (isset($data['shipping'])) {
            $total += floatval($data['shipping']);
        }
        
        switch ($paymentMethod) {
            case 'credit_card':
                return $this->processCardPayment($data, $total);
            case 'paypal':
                return $this->processPaypalPayment($data, $total);
            default:
                return false;
        }
    }
    
    /**
     * Process credit card payment
     * 
     * @param array $data Payment data
     * @param float $amount Payment amount
     * @return bool Success status
     */
    protected function processCardPayment(array $data, float $amount): bool
    {
        // In a real application, you would integrate with a payment processor like Stripe
        // This is a simplified example
        
        if ($this->config['mode'] === 'sandbox') {
            // Always return success in sandbox mode
            return true;
        }
        
        // Validate card details
        if (!$this->validateCardDetails($data)) {
            return false;
        }
        
        // Process with payment gateway
        try {
            // Simulate API call to payment processor
            // In production, you would use Stripe or another payment processor's API
            $success = $this->simulatePaymentProcessing();
            return $success;
        } catch (\Exception $e) {
            // Log payment error
            error_log('Payment processing error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process PayPal payment
     * 
     * @param array $data Payment data
     * @param float $amount Payment amount
     * @return bool Success status
     */
    protected function processPaypalPayment(array $data, float $amount): bool
    {
        // In a real application, you would integrate with PayPal's API
        // This is a simplified example
        
        if ($this->config['mode'] === 'sandbox') {
            // Always return success in sandbox mode
            return true;
        }
        
        try {
            // Simulate API call to PayPal
            $success = $this->simulatePaymentProcessing();
            return $success;
        } catch (\Exception $e) {
            // Log payment error
            error_log('PayPal payment processing error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate card details
     * 
     * @param array $data Card data
     * @return bool Validation result
     */
    protected function validateCardDetails(array $data): bool
    {
        // Check required fields
        $requiredFields = ['card_number', 'card_exp_month', 'card_exp_year', 'card_cvv'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        
        // Validate card number (basic Luhn algorithm check)
        $cardNumber = preg_replace('/\D/', '', $data['card_number']);
        if (!$this->validateLuhn($cardNumber)) {
            return false;
        }
        
        // Validate expiration date
        $expMonth = (int)$data['card_exp_month'];
        $expYear = (int)$data['card_exp_year'];
        
        if ($expMonth < 1 || $expMonth > 12) {
            return false;
        }
        
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');
        
        if ($expYear < $currentYear || ($expYear === $currentYear && $expMonth < $currentMonth)) {
            return false;
        }
        
        // Validate CVV
        $cvv = $data['card_cvv'];
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate card number using Luhn algorithm
     * 
     * @param string $number Card number
     * @return bool Validation result
     */
    protected function validateLuhn(string $number): bool
    {
        // Remove spaces and non-numeric characters
        $number = preg_replace('/\D/', '', $number);
        
        // Get length and checksum
        $length = strlen($number);
        $checksum = 0;
        
        // Process each digit
        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int)$number[$i];
            
            // Double every second digit
            if (($length - $i) % 2 === 0) {
                $digit *= 2;
                
                // If we have a two-digit number, add them together
                if ($digit > 9) {
                    $digit = $digit - 9;
                }
            }
            
            // Add to checksum
            $checksum += $digit;
        }
        
        // Check if divisible by 10
        return ($checksum % 10 === 0);
    }
    
    /**
     * Simulate payment processing (for testing)
     * 
     * @return bool Random success/failure
     */
    protected function simulatePaymentProcessing(): bool
    {
        // In testing/sandbox, simulate occasional failures
        // In production, this would be replaced with actual API calls
        return (rand(1, 10) > 2); // 80% success rate
    }
}