<?php
require_once __DIR__ . '/../../app/bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login?redirect=' . urlencode($_SERVER['HTTP_REFERER']));
    exit;
}

// Validate input
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$type = isset($_POST['type']) ? $_POST['type'] : '';

if (!$productId || !in_array($type, ['template', 'product'])) {
    $_SESSION['error'] = 'Invalid product information';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

try {
    // Add to cart
    global $pdo;
    $cartRepo = new \App\Repositories\CartRepository($pdo);
    $cartRepo->addItem($productId, $type, $_SESSION['user_id']);
    
    $_SESSION['success'] = 'Item added to cart successfully';
} catch (Exception $e) {
    $_SESSION['error'] = 'Failed to add item to cart: ' . $e->getMessage();
}

// Redirect back
header('Location: ' . $_SERVER['HTTP_REFERER']);
