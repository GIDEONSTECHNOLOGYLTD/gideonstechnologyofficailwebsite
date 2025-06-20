<?php
/**
 * OrderService - Manages order operations
 * 
 * This class handles order processing, status updates, and payment processing integration
 */
class OrderService {
    private $conn;
    private $logger;
    
    /**
     * Constructor
     * 
     * @param PDO|mysqli $dbConnection Database connection 
     */
    public function __construct($dbConnection = null) {
        $this->conn = $dbConnection;
        
        // Initialize a logger if available
        if (class_exists('Logger')) {
            $this->logger = new Logger('order');
        }
    }
    
    /**
     * Update the payment status of an order
     * 
     * @param string $orderId The order ID to update
     * @param string $status The new payment status
     * @return bool Success of the operation
     */
    public function updateOrderPaymentStatus($orderId, $status) {
        if (!$this->conn) {
            $this->logError('Database connection not available');
            return false;
        }
        
        try {
            if ($this->conn instanceof PDO) {
                $stmt = $this->conn->prepare("UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE order_id = ?");
                $result = $stmt->execute([$status, $orderId]);
            } else {
                // Assuming MySQLi
                $stmt = $this->conn->prepare("UPDATE orders SET payment_status = ?, updated_at = NOW() WHERE order_id = ?");
                $stmt->bind_param('ss', $status, $orderId);
                $result = $stmt->execute();
                $stmt->close();
            }
            
            if ($result) {
                $this->log("Order $orderId payment status updated to $status");
                
                // Trigger any notifications or follow-up actions
                $this->handleStatusChange($orderId, $status);
                
                return true;
            } else {
                $this->logError("Failed to update order $orderId payment status");
                return false;
            }
        } catch (Exception $e) {
            $this->logError("Error updating order payment status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process actions based on status changes
     * 
     * @param string $orderId The order ID
     * @param string $status The new status
     */
    private function handleStatusChange($orderId, $status) {
        switch ($status) {
            case 'paid':
                // Send confirmation email to customer
                $this->sendOrderConfirmation($orderId);
                break;
                
            case 'failed':
                // Notify customer of failed payment
                $this->sendPaymentFailureNotification($orderId);
                break;
                
            case 'refunded':
                // Send refund confirmation
                $this->sendRefundConfirmation($orderId);
                break;
        }
    }
    
    /**
     * Send order confirmation to customer
     * 
     * @param string $orderId The order ID
     */
    private function sendOrderConfirmation($orderId) {
        // Get order details
        $order = $this->getOrderById($orderId);
        if (!$order) return;
        
        // Simple email notification - in production use a proper email service
        $to = $order['customer_email'];
        $subject = "Your order #$orderId has been confirmed";
        $message = "Thank you for your order. Your payment has been processed successfully.";
        
        // Send email if mail function exists
        if (function_exists('mail')) {
            mail($to, $subject, $message, "From: orders@example.com");
        }
        
        $this->log("Order confirmation sent for order $orderId");
    }
    
    /**
     * Send payment failure notification to customer
     * 
     * @param string $orderId The order ID
     */
    private function sendPaymentFailureNotification($orderId) {
        // Get order details
        $order = $this->getOrderById($orderId);
        if (!$order) return;
        
        // Simple email notification
        $to = $order['customer_email'];
        $subject = "Payment failed for order #$orderId";
        $message = "We were unable to process your payment. Please update your payment information.";
        
        if (function_exists('mail')) {
            mail($to, $subject, $message, "From: orders@example.com");
        }
        
        $this->log("Payment failure notification sent for order $orderId");
    }
    
    /**
     * Send refund confirmation to customer
     * 
     * @param string $orderId The order ID
     */
    private function sendRefundConfirmation($orderId) {
        // Get order details
        $order = $this->getOrderById($orderId);
        if (!$order) return;
        
        // Simple email notification
        $to = $order['customer_email'];
        $subject = "Refund processed for order #$orderId";
        $message = "Your refund has been processed. Please allow 5-7 business days for the funds to appear in your account.";
        
        if (function_exists('mail')) {
            mail($to, $subject, $message, "From: orders@example.com");
        }
        
        $this->log("Refund confirmation sent for order $orderId");
    }
    
    /**
     * Get order by ID
     * 
     * @param string $orderId The order ID to retrieve
     * @return array|bool Order data or false on failure
     */
    public function getOrderById($orderId) {
        if (!$this->conn) {
            return false;
        }
        
        try {
            if ($this->conn instanceof PDO) {
                $stmt = $this->conn->prepare("SELECT * FROM orders WHERE order_id = ?");
                $stmt->execute([$orderId]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                // Assuming MySQLi
                $stmt = $this->conn->prepare("SELECT * FROM orders WHERE order_id = ?");
                $stmt->bind_param('s', $orderId);
                $stmt->execute();
                $result = $stmt->get_result();
                $order = $result->fetch_assoc();
                $stmt->close();
                return $order;
            }
        } catch (Exception $e) {
            $this->logError("Error retrieving order: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log an informational message
     * 
     * @param string $message The message to log
     */
    private function log($message) {
        if ($this->logger) {
            $this->logger->info($message);
        } else {
            error_log("[OrderService] [INFO] $message");
        }
    }
    
    /**
     * Log an error message
     * 
     * @param string $message The error message to log
     */
    private function logError($message) {
        if ($this->logger) {
            $this->logger->error($message);
        } else {
            error_log("[OrderService] [ERROR] $message");
        }
    }
}