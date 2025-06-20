<?php
/**
 * Product Repository
 * 
 * Handles database operations for products in GStore
 */

namespace App\Database\Repository;

use App\Utilities\Logger;

class ProductRepository extends BaseRepository
{
    /**
     * @var string The table name
     */
    protected $table = 'products';
    
    /**
     * Find products by category ID
     * 
     * @param int|string $categoryId
     * @return array
     */
    public function findByCategory($categoryId): array
    {
        return $this->findBy('category_id', $categoryId);
    }
    
    /**
     * Find featured products
     * 
     * @param int $limit
     * @return array
     */
    public function findFeatured(int $limit = 6): array
    {
        $query = "SELECT * FROM {$this->table} WHERE featured = 1 LIMIT :limit";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find products on sale
     * 
     * @param int $limit
     * @return array
     */
    public function findOnSale(int $limit = 8): array
    {
        $query = "SELECT * FROM {$this->table} WHERE sale_price IS NOT NULL AND sale_price > 0 LIMIT :limit";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Search products
     * 
     * @param string $searchTerm
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function search(string $searchTerm, ?int $limit = null, int $offset = 0): array
    {
        $searchTerm = "%{$searchTerm}%";
        
        $query = "SELECT * FROM {$this->table} WHERE name LIKE :term OR description LIKE :term";
        
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':term', $searchTerm);
        
        if ($limit !== null) {
            $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        }
        
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get product with its category info
     * 
     * @param int|string $productId
     * @return array|null
     */
    public function findWithCategory($productId): ?array
    {
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.id = :id";
                  
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $productId);
        $statement->execute();
        
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }
    
    /**
     * Update product stock
     * 
     * @param int|string $productId
     * @param int $quantity
     * @return bool
     */
    public function updateStock($productId, int $quantity): bool
    {
        $query = "UPDATE {$this->table} SET stock = stock - :quantity WHERE id = :id AND stock >= :quantity";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $productId);
        $statement->bindValue(':quantity', $quantity, \PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->rowCount() > 0;
    }
    
    /**
     * Get products with stock below threshold
     * 
     * @param int $threshold
     * @return array
     */
    public function getLowStockProducts(int $threshold = 5): array
    {
        return $this->findByMultiple(['stock <=' => $threshold]);
    }
}
