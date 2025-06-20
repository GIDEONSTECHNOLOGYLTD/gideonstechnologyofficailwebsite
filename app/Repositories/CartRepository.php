<?php
namespace App\Repositories;

use PDO;

/**
 * Manages user cart items
 */
class CartRepository {
    protected PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Add item to cart or increment quantity
     */
    public function addItem(int $productId, string $type, int $userId, int $quantity = 1): bool {
        $stmt = $this->pdo->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND product_type = ?');
        $stmt->execute([$userId, $productId, $type]);
        $existing = $stmt->fetch(PDO::FETCH_OBJ);
        
        // For templates, only allow one in cart
        if ($type === 'template') {
            $quantity = 1;
        }
        
        if ($existing) {
            if ($type === 'template') {
                return true; // Template already in cart
            }
            $stmt = $this->pdo->prepare('UPDATE cart_items SET quantity = quantity + ? WHERE id = ?');
            return $stmt->execute([$quantity, $existing->id]);
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO cart_items (user_id, product_id, product_type, quantity) VALUES (?, ?, ?, ?)');
            return $stmt->execute([$userId, $productId, $type, $quantity]);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartItemId): bool {
        $stmt = $this->pdo->prepare('DELETE FROM cart_items WHERE id = ?');
        return $stmt->execute([$cartItemId]);
    }

    /**
     * Get all cart items for a user
     */
    public function getItems(int $userId): array {
        $sql = 'SELECT ci.id AS cart_id, p.id AS product_id, p.name, p.price, ci.quantity
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Clear user's cart
     */
    public function clearCart(int $userId): bool {
        $stmt = $this->pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
        return $stmt->execute([$userId]);
    }
}
