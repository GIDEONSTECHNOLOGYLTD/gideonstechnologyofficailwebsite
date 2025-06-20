<?php
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Repositories\TemplateRepository;

// Ensure user is logged in
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

// Validate purchase_id
$purchaseId = isset($_GET['purchase_id']) ? (int)$_GET['purchase_id'] : null;
if (!$purchaseId) {
    header('Location: ' . SITE_URL . '/templates');
    exit;
}

// Fetch purchase record
$repo = new TemplateRepository();
$purchase = $repo->getPurchaseById($purchaseId, $_SESSION['user_id']);
if (!$purchase) {
    http_response_code(403);
    exit('Unauthorized access');
}

// Determine file path from download_url field
$fileRel = ltrim($purchase->download_url, '/');
$filePath = BASE_PATH . '/public/' . $fileRel;

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File not found');
}

// Stream file for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
