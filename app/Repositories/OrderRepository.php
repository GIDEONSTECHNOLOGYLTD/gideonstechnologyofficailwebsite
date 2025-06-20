<?php
namespace App\Repositories;

use PDO;

/**
 * Manages orders and order items
 */
class OrderRepository {
    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new order
     * @return int Inserted order ID
     */
    public function create(int $userId, float $total, string $shippingAddress, string $paymentMethod, string $paymentStatus): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, payment_status, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $status = $paymentStatus === 'paid' ? 'completed' : 'pending';
        $stmt->execute([$userId, $total, $shippingAddress, $paymentMethod, $paymentStatus, $status]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Add an item to an order
     */
    public function addItem(int $orderId, int $productId, int $quantity, float $price): bool {
        $stmt = $this->pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, quantity, price, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())'
        );
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }

    /**
     * Retrieve an order by ID
     */
    public function getById(int $orderId) {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get all orders for a user
     * @param int $userId
     * @return array
     */
    public function getByUserId(int $userId): array {
        // Using optimized index: idx_orders_user_created
        $stmt = $this->pdo->prepare(
            'SELECT o.*, 
                COUNT(oi.id) as item_count, 
                SUM(oi.quantity) as total_items,
                GROUP_CONCAT(DISTINCT p.name ORDER BY p.name ASC SEPARATOR ", ") as product_names
            FROM orders o
                FORCE INDEX (idx_orders_user_created)
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN products p ON p.id = oi.product_id
            WHERE o.user_id = ?
            GROUP BY o.id, o.created_at
            ORDER BY o.created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get items for a given order
     * @return array of stdClass
     */
    public function getItems(int $orderId): array {
        // Using optimized index: idx_order_items_order_product
        $sql = 'SELECT oi.id, oi.quantity, oi.price, 
                p.name, p.sku, p.id as product_id
                FROM order_items oi
                    FORCE INDEX (idx_order_items_order_product)
                    LEFT JOIN products p ON p.id = oi.product_id
                WHERE oi.order_id = ?
                ORDER BY p.name ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update payment status and order status
     */
    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool {
        $status = $paymentStatus === 'paid' ? 'completed' : 'pending';
        try {
            $this->pdo->beginTransaction();
            
            // Using primary key for fast update
            $stmt = $this->pdo->prepare(
                'UPDATE orders 
                SET payment_status = ?, 
                    status = ?, 
                    updated_at = NOW() 
                WHERE id = ?'
            );
            $result = $stmt->execute([$paymentStatus, $status, $orderId]);
            
            $this->pdo->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log('Error updating payment status: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all orders for a user
     * @param int $userId
     * @return array of stdClass
     */
    public function getByUser(int $userId): array {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
