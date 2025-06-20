<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Order Model
 * Handles business logic related to orders
 */
class Order extends BaseModel
{
    protected $table = 'orders';
    protected $itemsTable = 'order_items';
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total',
        'shipping_method',
        'shipping_cost',
        'tax',
        'discount',
        'notes',
        'billing_address_id',
        'shipping_address_id',
        'payment_method',
        'payment_status',
        'tracking_number'
    ];
    
    /**
     * Statuses for orders
     * 
     * @var array
     */
    private $statuses = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded'
    ];
    
    /**
     * Get available order statuses
     * 
     * @return array Order statuses
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }
    
    /**
     * Create a new order
     * 
     * @param array $data Order data
     * @return int|false The new order ID or false on failure
     */
    public function createOrder(array $data)
    {
        return $this->create($data);
    }
    
    /**
     * Add an item to an order
     * 
     * @param int $orderId Order ID
     * @param array $data Item data
     * @return int|false The new item ID or false on failure
     */
    public function createOrderItem(int $orderId, array $data)
    {
        $data['order_id'] = $orderId;
        
        $fields = array_keys($data);
        $placeholders = array_map(function($field) { 
            return ":{$field}"; 
        }, $fields);
        
        $fieldsStr = implode(', ', $fields);
        $placeholdersStr = implode(', ', $placeholders);
        
        $sql = "INSERT INTO {$this->itemsTable} ({$fieldsStr}) VALUES ({$placeholdersStr})";
        $stmt = Database::getConnection()->prepare($sql);
        
        foreach ($data as $field => $value) {
            $stmt->bindValue(":{$field}", $value);
        }
        
        if ($stmt->execute()) {
            return Database::lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update order status
     * 
     * @param int $orderId Order ID
     * @param string|array $data Status string or data array with status
     * @return bool Success status
     */
    public function updateStatus(int $orderId, $data): bool
    {
        // If data is a string, convert it to an array
        if (is_string($data)) {
            $data = [
                'status' => $data,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        // Make sure we have a status
        if (!isset($data['status'])) {
            return false;
        }
        
        return $this->update($orderId, $data);
    }
    
    /**
     * Get order items
     * 
     * @param int $orderId Order ID
     * @return array Order items
     */
    public function getOrderItems(int $orderId): array
    {
        $sql = "
            SELECT oi.*, p.name, p.sku, p.image
            FROM {$this->itemsTable} oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ";
        
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Get order items
     * 
     * @param int $orderId Order ID
     * @return array Order items
     */
    public function findOrderItems(int $orderId): array
    {
        return $this->getOrderItems($orderId);
    }
    
    /**
     * Find order by ID
     * 
     * @param int $orderId Order ID
     * @return object|null Order data
     */
    public function findById(int $orderId): ?object
    {
        return $this->find($orderId);
    }
    
    /**
     * Get total revenue
     * 
     * @return float Total revenue
     */
    public function calculateTotalRevenue(): float
    {
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} WHERE status = 'completed'";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return (float)($result->revenue ?? 0);
    }
    
    /**
     * Get recent orders
     * 
     * @param int $limit Number of orders to return
     * @return array Recent orders
     */
    public function getRecent(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Count all orders
     * 
     * @return int Number of orders
     */
    public function countAll(): int
    {
        return $this->count();
    }
    
    /**
     * Find orders between dates
     * 
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @param string|null $status Filter by status
     * @return array Orders between dates
     */
    public function findAllBetweenDates(string $startDate, string $endDate, ?string $status = null): array
    {
        $params = [];
        $whereConditions = [];
        
        // Date range condition
        $whereConditions[] = "DATE(created_at) BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $startDate;
        $params[':end_date'] = $endDate;
        
        // Status condition (if provided)
        if ($status) {
            $whereConditions[] = "status = :status";
            $params[':status'] = $status;
        }
        
        // Build WHERE clause
        $whereClause = implode(' AND ', $whereConditions);
        
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY created_at DESC";
        $stmt = Database::getConnection()->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Calculate sales totals between dates
     * 
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return object Sales totals
     */
    public function calculateSalesTotals(string $startDate, string $endDate): object
    {
        $sql = "
            SELECT 
                COUNT(*) as order_count,
                SUM(total) as total_sales,
                AVG(total) as average_order_value,
                COUNT(DISTINCT user_id) as customer_count
            FROM {$this->table}
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date
            AND status NOT IN ('cancelled', 'refunded')
        ";
        
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Find orders for CSV export
     * 
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @param string|null $status Filter by status
     * @return array Orders for export
     */
    public function findForExport(string $startDate, string $endDate, ?string $status = null): array
    {
        return $this->findAllBetweenDates($startDate, $endDate, $status);
    }
    
    /**
     * Get order totals by status
     * 
     * @return array Order counts by status
     */
    public function getStatusCounts(): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        $counts = [];
        
        foreach ($results as $result) {
            $counts[$result->status] = $result->count;
        }
        
        return $counts;
    }
}
