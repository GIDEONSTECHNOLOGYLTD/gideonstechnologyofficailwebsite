<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Cart Model
 * 
 * Handles shopping cart operations
 */
class Cart extends Model
{
    /**
     * Table name
     * 
     * @var string
     */
    protected $table = 'cart_items';
    
    /**
     * Cart items in session
     * 
     * @var array
     */
    protected $items = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initializeCart();
    }
    
    /**
     * Initialize cart from session
     * 
     * @return void
     */
    protected function initializeCart(): void
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $this->items = $_SESSION['cart'];
    }
    
    /**
     * Get all cart items
     * 
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * Add item to cart
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity (default: 1)
     * @param array $options Product options (default: [])
     * @return bool
     */
    public function add(int $productId, int $quantity = 1, array $options = []): bool
    {
        // Get product info
        $product = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $product->execute([$productId]);
        $productData = $product->fetch(PDO::FETCH_ASSOC);
        
        if (!$productData) {
            return false;
        }
        
        // Generate cart item key
        $cartKey = $this->generateCartKey($productId, $options);
        
        // Check if item exists in cart
        if (isset($this->items[$cartKey])) {
            // Update quantity
            $this->items[$cartKey]['quantity'] += $quantity;
        } else {
            // Add new item
            $this->items[$cartKey] = [
                'id' => $productId,
                'name' => $productData['name'],
                'price' => $productData['price'],
                'quantity' => $quantity,
                'options' => $options,
                'image' => $productData['image'] ?? null
            ];
        }
        
        // Update session
        $this->updateSession();
        
        return true;
    }
    
    /**
     * Update cart item quantity
     * 
     * @param mixed $id Cart item key or ID
     * @param array|int $data Array of data or quantity if direct update
     * @return bool
     */
    public function update($id, $data)
    {
        // Handle direct quantity update for backward compatibility
        if (is_int($data)) {
            $cartKey = $id;
            $quantity = $data;
            
            if (!isset($this->items[$cartKey])) {
                return false;
            }
            
            if ($quantity <= 0) {
                // Remove item if quantity is zero or negative
                return $this->remove($cartKey);
            }
            
            $this->items[$cartKey]['quantity'] = $quantity;
        } else {
            // Handle array data update
            if (!is_array($data)) {
                return false;
            }
            
            $cartKey = $id;
            
            if (!isset($this->items[$cartKey])) {
                return false;
            }
            
            // Update all provided fields
            foreach ($data as $key => $value) {
                if (in_array($key, ['quantity', 'options', 'price', 'name', 'image'])) {
                    $this->items[$cartKey][$key] = $value;
                }
            }
        }
        
        // Update quantity
        $this->items[$cartKey]['quantity'] = $quantity;
        
        // Update session
        $this->updateSession();
        
        return true;
    }
    
    /**
     * Remove item from cart
     * 
     * @param string $cartKey Cart item key
     * @return bool
     */
    public function remove(string $cartKey): bool
    {
        if (!isset($this->items[$cartKey])) {
            return false;
        }
        
        // Remove item
        unset($this->items[$cartKey]);
        
        // Update session
        $this->updateSession();
        
        return true;
    }
    
    /**
     * Clear all items from cart
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->items = [];
        $_SESSION['cart'] = [];
    }
    
    /**
     * Calculate cart subtotal
     * 
     * @return float
     */
    public function calculateSubtotal(): float
    {
        $subtotal = 0;
        
        foreach ($this->items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        return $subtotal;
    }
    
    /**
     * Calculate cart tax
     * 
     * @param float $taxRate Tax rate (default: 0.1 for 10%)
     * @return float
     */
    public function calculateTax(float $taxRate = 0.1): float
    {
        $subtotal = $this->calculateSubtotal();
        return $subtotal * $taxRate;
    }
    
    /**
     * Calculate shipping cost
     * 
     * @param string $shippingMethod Shipping method (default: 'standard')
     * @return float
     */
    public function calculateShipping(string $shippingMethod = 'standard'): float
    {
        $shippingRates = [
            'standard' => 5.99,
            'express' => 12.99,
            'overnight' => 24.99
        ];
        
        return $shippingRates[$shippingMethod] ?? $shippingRates['standard'];
    }
    
    /**
     * Calculate cart total (subtotal + tax + shipping)
     * 
     * @param float $taxRate Tax rate
     * @param string $shippingMethod Shipping method
     * @return float
     */
    public function calculateTotal(float $taxRate = 0.1, string $shippingMethod = 'standard'): float
    {
        $subtotal = $this->calculateSubtotal();
        $tax = $this->calculateTax($taxRate);
        $shipping = $this->calculateShipping($shippingMethod);
        
        return $subtotal + $tax + $shipping;
    }
    
    /**
     * Count items in cart
     * 
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
    
    /**
     * Count total quantity of items in cart
     * 
     * @return int
     */
    public function totalQuantity(): int
    {
        $total = 0;
        
        foreach ($this->items as $item) {
            $total += $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Check if cart is empty
     * 
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
    
    /**
     * Generate unique cart key for a product with options
     * 
     * @param int $productId Product ID
     * @param array $options Product options
     * @return string
     */
    protected function generateCartKey(int $productId, array $options): string
    {
        $key = $productId;
        
        if (!empty($options)) {
            // Sort options by key to ensure consistent key generation
            ksort($options);
            $key .= '_' . md5(json_encode($options));
        }
        
        return (string)$key;
    }
    
    /**
     * Update cart session data
     * 
     * @return void
     */
    protected function updateSession(): void
    {
        $_SESSION['cart'] = $this->items;
    }
    
    /**
     * Find all cart items by user ID from database
     * 
     * @param int $userId User ID
     * @return array
     */
    public function findAllByUser(int $userId): array
    {
        $query = $this->db->prepare("
            SELECT ci.*, p.name, p.price, p.image 
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ?
        ");
        
        $query->execute([$userId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Save cart items to database for a user
     * 
     * @param int $userId User ID
     * @return bool
     */
    public function saveToDatabase(int $userId): bool
    {
        // First clear existing cart items for this user
        $delete = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $delete->execute([$userId]);
        
        // Insert new cart items
        $insert = $this->db->prepare("
            INSERT INTO cart_items (user_id, product_id, quantity, options)
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($this->items as $item) {
            $options = !empty($item['options']) ? json_encode($item['options']) : null;
            $insert->execute([
                $userId,
                $item['id'],
                $item['quantity'],
                $options
            ]);
        }
        
        return true;
    }
    
    /**
     * Load cart items from database for a user
     * 
     * @param int $userId User ID
     * @return bool
     */
    public function loadFromDatabase(int $userId): bool
    {
        $query = $this->db->prepare("
            SELECT ci.*, p.name, p.price, p.image 
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.user_id = ?
        ");
        
        $query->execute([$userId]);
        $items = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Reset cart
        $this->items = [];
        
        foreach ($items as $item) {
            $options = !empty($item['options']) ? json_decode($item['options'], true) : [];
            $cartKey = $this->generateCartKey($item['product_id'], $options);
            
            $this->items[$cartKey] = [
                'id' => $item['product_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'options' => $options,
                'image' => $item['image']
            ];
        }
        
        // Update session
        $this->updateSession();
        
        return true;
    }
}
