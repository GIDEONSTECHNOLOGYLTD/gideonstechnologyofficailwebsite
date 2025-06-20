<?php
namespace App\Controllers;

use App\Models\Cart;
use Exception;

class CartController extends BaseController {
    protected $cart;
    
    public function __construct() {
        parent::__construct();
        $this->cart = new Cart();
    }
    
    public function addItem() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['product_id']) || !isset($input['quantity'])) {
            throw new Exception('Product ID and quantity are required', 400);
        }
        
        $productId = intval($input['product_id']);
        $quantity = max(1, intval($input['quantity']));
        $userId = $_SESSION['user_id'] ?? null;
        
        $item = $this->cart->addItem($userId, $productId, $quantity);
        
        return [
            'success' => true,
            'message' => 'Item added to cart',
            'cart_item' => $item
        ];
    }
    
    // Other existing cart methods would be preserved
}