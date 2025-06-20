<?php
/**
 * Order Item Repository
 * 
 * Handles database operations for order items
 */

namespace App\Database\Repository;

class OrderItemRepository extends BaseRepository
{
    /**
     * @var string The table name
     */
    protected $table = 'order_items';
    
    /**
     * Find items by order ID
     * 
     * @param int|string $orderId
     * @return array
     */
    public function findByOrder($orderId): array
    {
        return $this->findBy('order_id', $orderId);
    }
    
    /**
     * Get order items with product details
     * 
     * @param int|string $orderId
     * @return array
     */
    public function getItemsWithProductDetails($orderId): array
    {
        $query = "SELECT i.*, p.name as product_name, p.sku, p.image_url
                  FROM {$this->table} i
                  LEFT JOIN products p ON i.product_id = p.id
                  WHERE i.order_id = :order_id";
                  
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':order_id', $orderId);
        $statement->execute();
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get popular products (based on order frequency)
     * 
     * @param int $limit
     * @return array
     */
    public function getPopularProducts(int $limit = 5): array
    {
        $query = "SELECT p.id, p.name, p.price, p.image_url, 
                         COUNT(i.id) as order_count, 
                         SUM(i.quantity) as total_quantity
                  FROM {$this->table} i
                  JOIN products p ON i.product_id = p.id
                  GROUP BY p.id
                  ORDER BY total_quantity DESC
                  LIMIT :limit";
                  
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
