<?php
/**
 * Order Repository
 * 
 * Handles database operations for customer orders
 */

namespace App\Database\Repository;

use App\Database\DatabaseManager;
use App\Utilities\Logger;

class OrderRepository extends BaseRepository
{
    /**
     * @var string The table name
     */
    protected $table = 'orders';
    
    /**
     * Find orders by user ID
     * 
     * @param int|string $userId
     * @return array
     */
    public function findByUser($userId): array
    {
        return $this->findBy('user_id', $userId);
    }
    
    /**
     * Create a new order with its items
     * 
     * @param array $orderData Order data
     * @param array $items Order items (array of product_id => quantity)
     * @return int|string|null Order ID if successful, null otherwise
     */
    public function createOrder(array $orderData, array $items)
    {
        $db = DatabaseManager::getInstance();
        
        try {
            // Start transaction
            $db->beginTransaction();
            
            // Create order
            $orderId = $this->create($orderData);
            
            if (!$orderId) {
                throw new \Exception("Failed to create order");
            }
            
            // Create order items
            $orderItemRepo = new OrderItemRepository();
            foreach ($items as $productId => $quantity) {
                $item = [
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Get product details
                $productRepo = new ProductRepository();
                $product = $productRepo->findById($productId);
                
                if (!$product) {
                    throw new \Exception("Product not found: {$productId}");
                }
                
                // Add price to order item
                $item['price'] = $product['sale_price'] ?? $product['price'];
                
                // Create order item
                $orderItemRepo->create($item);
                
                // Update product stock
                $productRepo->updateStock($productId, $quantity);
            }
            
            // Commit transaction
            $db->commit();
            
            return $orderId;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $db->rollback();
            Logger::error("Failed to create order: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get order with all its items
     * 
     * @param int|string $orderId
     * @return array|null
     */
    public function getOrderWithItems($orderId): ?array
    {
        // Get order details
        $order = $this->findById($orderId);
        
        if (!$order) {
            return null;
        }
        
        // Get order items
        $orderItemRepo = new OrderItemRepository();
        $items = $orderItemRepo->findByOrder($orderId);
        
        $order['items'] = $items;
        
        // Calculate order totals
        $order['subtotal'] = array_reduce($items, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
        
        // Tax rate could come from config or settings table
        $taxRate = 0.08; // 8%
        $order['tax'] = $order['subtotal'] * $taxRate;
        $order['total'] = $order['subtotal'] + $order['tax'];
        
        return $order;
    }
    
    /**
     * Update order status
     * 
     * @param int|string $orderId
     * @param string $status
     * @return bool
     */
    public function updateStatus($orderId, string $status): bool
    {
        return $this->update($orderId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get recent orders
     * 
     * @param int $limit
     * @return array
     */
    public function getRecentOrders(int $limit = 10): array
    {
        $query = "SELECT o.*, u.name as customer_name 
                  FROM {$this->table} o
                  LEFT JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC
                  LIMIT :limit";
        
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get orders by status
     * 
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        return $this->findBy('status', $status);
    }
    
    /**
     * Get sales statistics for a date range
     * 
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array
     */
    public function getSalesStats(string $startDate, string $endDate): array
    {
        $query = "SELECT COUNT(*) as order_count, 
                         SUM(total_amount) as total_sales,
                         AVG(total_amount) as average_order
                  FROM {$this->table}
                  WHERE created_at BETWEEN :start AND :end
                  AND status != 'cancelled'";
        
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':start', $startDate . ' 00:00:00');
        $statement->bindValue(':end', $endDate . ' 23:59:59');
        $statement->execute();
        
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
}
