<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Utilities\Logger;
use Stripe\StripeClient;
use PayPal\Rest\ApiContext;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Yabacon\Paystack;

/**
 * Payment Service
 * 
 * Handles payment processing for various payment methods
 */
class PaymentService
{
    protected $stripe;
    protected $paypal;
    protected $paystack;

    public function __construct(StripeClient $stripe, ApiContext $paypal, Paystack $paystack)
    {
        $this->stripe = $stripe;
        $this->paypal = $paypal;
        $this->paystack = $paystack;
    }

    public function initializeStripe($userId, $orderId, $amount, $currency = 'usd')
    {
        try {
            $data = array();
            $data['payment_method_types'] = array('card');
            $data['line_items'] = array(
                array(
                    'price_data' => array(
                        'currency' => $currency,
                        'unit_amount' => (int) ($amount * 100),
                        'product_data' => array(
                            'name' => 'Order #' . $orderId
                        )
                    ),
                    'quantity' => 1
                )
            );
            $data['mode'] = 'payment';
            $data['success_url'] = env('APP_URL') . '/payment/success?session_id={CHECKOUT_SESSION_ID}';
            $data['cancel_url'] = env('APP_URL') . '/payment/cancel';
            $data['client_reference_id'] = $orderId;
            $data['customer_email'] = $this->getUserEmail($userId);

            $session = $this->stripe->checkout->sessions->create($data);
            return $session->url;
        } catch (\Exception $e) {
            throw new \Exception('Failed to initialize Stripe payment: ' . $e->getMessage());
        }
    }

    public function initializePaystack($userId, $orderId, $amount, $currency = 'NGN')
    {
        try {
            $data = array();
            $data['amount'] = (int) ($amount * 100);
            $data['email'] = $this->getUserEmail($userId);
            $data['currency'] = $currency;
            $data['metadata'] = array(
                'order_id' => $orderId,
                'user_id' => $userId
            );
            $data['callback_url'] = getenv('SITE_URL') . '/services/gstore/confirm/paystack';
            $tranx = $this->paystack->transaction->initialize($data);
            return $tranx->data->authorization_url;
        } catch (\Exception $e) {
            throw new \Exception('Failed to initialize Paystack payment: ' . $e->getMessage());
        }
    }

    public function initializePayPal($userId, $orderId, $amount, $currency = 'USD')
    {
        try {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $amountObj = new Amount();
            $amountObj->setTotal($amount)
                ->setCurrency($currency);

            $transaction = new Transaction();
            $transaction->setAmount($amountObj)
                ->setDescription('Order #' . $orderId);

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(env('APP_URL') . '/payment/success')
                ->setCancelUrl(env('APP_URL') . '/payment/cancel');

            $payment = new Payment();
            $payment->setIntent('sale')
                ->setPayer($payer)
                ->setTransactions(array($transaction))
                ->setRedirectUrls($redirectUrls);

            $payment->create($this->paypal);
            return $payment->getApprovalLink();
        } catch (\Exception $e) {
            throw new \Exception('Failed to initialize PayPal payment: ' . $e->getMessage());
        }
    }

    /**
     * Process credit card payment
     * 
     * @param array $data Payment data
     * @return array Payment result
     */
    public function processCardPayment(array $data): array
    {
        Logger::info('Processing credit card payment for order: ' . $data['order_id']);
        
        // In a real application, this would integrate with a payment gateway
        // For testing purposes, we'll simulate a successful payment
        
        // Validate credit card data (basic validation)
        $cardNumber = preg_replace('/\s+/', '', $data['card_number']);
        $cardExpMonth = $data['card_exp_month'];
        $cardExpYear = $data['card_exp_year'];
        $cardCvv = $data['card_cvv'];
        
        // Check if card is expired
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');
        
        if ($cardExpYear < $currentYear || 
            ($cardExpYear == $currentYear && $cardExpMonth < $currentMonth)) {
            Logger::error('Credit card payment failed: Card expired');
            return [
                'success' => false,
                'message' => 'Card expired',
                'error_code' => 'card_expired'
            ];
        }
        
        // Simple Luhn algorithm check for card number validity
        if (!$this->isValidCreditCard($cardNumber)) {
            Logger::error('Credit card payment failed: Invalid card number');
            return [
                'success' => false,
                'message' => 'Invalid card number',
                'error_code' => 'invalid_card'
            ];
        }
        
        // Generate a payment ID (would come from payment gateway in real app)
        $paymentId = 'CC_' . time() . '_' . substr(md5($data['order_id'] . $cardNumber), 0, 8);
        
        Logger::info('Credit card payment successful for order: ' . $data['order_id']);
        
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'payment_id' => $paymentId,
            'transaction_date' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Process PayPal payment
     * 
     * @param array $data Payment data
     * @return array Payment result
     */
    public function processPayPalPayment(array $data): array
    {
        Logger::info('Processing PayPal payment for order: ' . $data['order_id']);
        
        // In a real application, this would redirect to PayPal and handle the return
        // For demo purposes, we'll simulate a successful payment
        
        // Generate a payment ID (would come from PayPal in real app)
        $paymentId = 'PP_' . time() . '_' . substr(md5($data['order_id']), 0, 8);
        
        Logger::info('PayPal payment successful for order: ' . $data['order_id']);
        
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'payment_id' => $paymentId,
            'transaction_date' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Process refund for an order
     * 
     * @param int $orderId Order ID
     * @param float $amount Amount to refund (default: full amount)
     * @return array Refund result
     */
    public function processRefund(int $orderId, float $amount = 0): array
    {
        // Get the order from the database
        try {
            $order = $this->findOrderOrFail($orderId);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'error_code' => 'order_not_found'
            ];
        }
        
        // Check if order can be refunded
        if (!in_array($order->status, ['completed', 'processing', 'shipped', 'paid'])) {
            return [
                'success' => false,
                'message' => 'Order cannot be refunded',
                'error_code' => 'invalid_order_status'
            ];
        }
        
        // If no amount specified, refund the full amount
        if ($amount <= 0) {
            $amount = $order->total;
        }
        
        // Check if amount is valid
        if ($amount > $order->total) {
            return [
                'success' => false,
                'message' => 'Refund amount exceeds order total',
                'error_code' => 'invalid_amount'
            ];
        }
        
        // Process refund based on the payment method
        $result = [];
        
        switch ($order->payment_method) {
            case 'credit_card':
                $result = $this->processCreditCardRefund($order, $amount);
                break;
                
            case 'paypal':
                $result = $this->processPayPalRefund($order, $amount);
                break;
                
            case 'paystack':
                $result = $this->processPaystackRefund($order, $amount);
                break;
                
            default:
                $result = [
                    'success' => false,
                    'message' => 'Unsupported payment method for refund',
                    'error_code' => 'unsupported_payment_method'
                ];
                break;
        }
        
        // If refund was successful, update order status
        if ($result['success']) {
            $orderModel = new \App\Models\Order();
            $orderModel->updateStatus($orderId, [
                'refunded_amount' => $amount,
                'status' => $amount >= $order->total ? 'refunded' : 'partially_refunded',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return $result;
    }
    
    /**
     * Process credit card refund
     * 
     * @param object $order Order object
     * @param float $amount Amount to refund
     * @return array Refund result
     */
    protected function processCreditCardRefund(object $order, float $amount): array
    {
        Logger::info('Processing credit card refund for order: ' . $order->id);
        
        // In a real application, this would integrate with a payment gateway
        // For testing purposes, we'll simulate a successful refund
        
        if (empty($order->payment_id)) {
            Logger::error('Credit card refund failed: No payment ID');
            return [
                'success' => false,
                'message' => 'No payment ID found for this order',
                'error_code' => 'missing_payment_id'
            ];
        }
        
        // Generate a refund ID (would come from payment gateway in real app)
        $refundId = 'REF_' . time() . '_' . substr(md5($order->id), 0, 8);
        
        Logger::info('Credit card refund successful for order: ' . $order->id);
        
        return [
            'success' => true,
            'message' => 'Refund processed successfully',
            'refund_id' => $refundId,
            'amount' => $amount,
            'transaction_date' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Process PayPal refund
     * 
     * @param object $order Order object
     * @param float $amount Amount to refund
     * @return array Refund result
     */
    protected function processPayPalRefund(object $order, float $amount): array
    {
        Logger::info('Processing PayPal refund for order: ' . $order->id);
        
        // In a real application, this would integrate with PayPal API
        // For testing purposes, we'll simulate a successful refund
        
        if (empty($order->payment_id)) {
            Logger::error('PayPal refund failed: No payment ID');
            return [
                'success' => false,
                'message' => 'No payment ID found for this order',
                'error_code' => 'missing_payment_id'
            ];
        }
        
        // Generate a refund ID (would come from PayPal in real app)
        $refundId = 'PPREF_' . time() . '_' . substr(md5($order->id), 0, 8);
        
        Logger::info('PayPal refund successful for order: ' . $order->id);
        
        return [
            'success' => true,
            'message' => 'Refund processed successfully',
            'refund_id' => $refundId,
            'amount' => $amount,
            'transaction_date' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Process Paystack refund
     * 
     * @param object $order Order object
     * @param float $amount Amount to refund
     * @return array Refund result
     */
    protected function processPaystackRefund(object $order, float $amount): array
    {
        Logger::info('Processing Paystack refund for order: ' . $order->id);
        
        // In a real application, this would integrate with Paystack API
        // For testing purposes, we'll simulate a successful refund
        
        if (empty($order->payment_id)) {
            Logger::error('Paystack refund failed: No payment ID');
            return [
                'success' => false,
                'message' => 'No payment ID found for this order',
                'error_code' => 'missing_payment_id'
            ];
        }
        
        // Generate a refund ID (would come from Paystack in real app)
        $refundId = 'PSREF_' . time() . '_' . substr(md5($order->id), 0, 8);
        
        Logger::info('Paystack refund successful for order: ' . $order->id);
        
        return [
            'success' => true,
            'message' => 'Refund processed successfully',
            'refund_id' => $refundId,
            'amount' => $amount,
            'transaction_date' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Check if credit card number is valid using Luhn algorithm
     * 
     * @param string $cardNumber Credit card number
     * @return bool Is valid
     */
    private function isValidCreditCard(string $cardNumber): bool
    {
        // Remove any non-digits
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        
        // Check length
        $length = strlen($cardNumber);
        if ($length < 13 || $length > 19) {
            return false;
        }
        
        // Luhn algorithm
        $sum = 0;
        $doubleUp = false;
        
        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int)$cardNumber[$i];
            
            if ($doubleUp) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
            $doubleUp = !$doubleUp;
        }
        
        return ($sum % 10 === 0);
    }

    private function getUserEmail($userId)
    {
        try {
            $userModel = new User();
            $user = $userModel->find($userId);
            
            if (!$user) {
                throw new \Exception("User not found with ID: {$userId}");
            }
            
            return $user->email;
        } catch (\Exception $e) {
            throw new \Exception('Failed to get user email: ' . $e->getMessage());
        }
    }

    /**
     * Find order by ID or fail
     * 
     * @param int $orderId Order ID
     * @return object Order object
     * @throws \Exception If order not found
     */
    public function findOrderOrFail(int $orderId): object
    {
        $orderModel = new \App\Models\Order();
        $order = $orderModel->find($orderId);
        
        if (!$order) {
            throw new \Exception("Order not found with ID: {$orderId}");
        }
        
        return $order;
    }
}
