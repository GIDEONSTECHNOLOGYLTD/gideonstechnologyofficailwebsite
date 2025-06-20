<?php
require_once __DIR__ . '/../../app/bootstrap.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}
$template_id = $_POST['template_id'] ?? null;
if (!$template_id) {
    header('Location: ' . SITE_URL . '/templates');
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM templates WHERE id = ?');
$stmt->execute([$template_id]);
$template = $stmt->fetch();
if (!$template) {
    header('Location: ' . SITE_URL . '/templates');
    exit;
}
// Record template purchase
$stmtPurchase = $pdo->prepare("INSERT INTO template_purchases (user_id, template_id, purchase_date) VALUES (?, ?, NOW())");
$stmtPurchase->execute([$_SESSION['user_id'], $template_id]);

$page_title = 'Purchase Confirmation - ' . SITE_NAME;
include_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5">
    <h1>Thank You for Your Purchase</h1>
    <p>You have successfully purchased the template <strong><?php echo htmlspecialchars($template->name); ?></strong>.</p>
    <a href="<?php echo SITE_URL; ?>/templates" class="btn btn-primary">Back to Templates</a>
    <a href="<?php echo SITE_URL; ?>/account" class="btn btn-outline-secondary">View My Account</a>
</div>
<?php include_once __DIR__ . '/../includes/footer.php';
?>
