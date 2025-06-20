<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
use App\Repositories\ProductRepository;

// Admin check
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login.php'); exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $repo = new ProductRepository($pdo);
    $repo->delete($id);
}
// Redirect back to list
header('Location: ' . SITE_URL . '/admin/products');
exit;
