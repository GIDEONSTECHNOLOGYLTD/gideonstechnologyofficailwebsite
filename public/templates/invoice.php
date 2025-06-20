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

// Generate simple HTML invoice
$page_title = 'Invoice #' . $purchase->purchase_id . ' - ' . SITE_NAME;
include_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5">
    <h1>Invoice #<?php echo $purchase->purchase_id; ?></h1>
    <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($purchase->purchase_date)); ?></p>
    <p><strong>Template:</strong> <?php echo htmlspecialchars($purchase->name); ?></p>
    <p><strong>Price:</strong> $<?php echo number_format($purchase->price, 2); ?></p>
    <hr>
    <h3>Billing Information</h3>
    <p>Name: <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
    <p>Email: <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
</div>
<?php include_once __DIR__ . '/../includes/footer.php';
