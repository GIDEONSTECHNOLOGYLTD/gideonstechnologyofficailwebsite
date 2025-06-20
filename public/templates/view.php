<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../includes/db.php';

// Get template ID from URL
$template_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get template details
$stmt = $pdo->prepare('SELECT * FROM templates WHERE id = ?');
$stmt->execute([$template_id]);
$template = $stmt->fetch(PDO::FETCH_ASSOC);

// If template not found, redirect to templates page
if (!$template) {
    header('Location: /templates');
    exit;
}

$page_title = $template['name'] . " - " . SITE_NAME;
$meta_description = $template['description'];

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Template Details Section -->
<section class="template-details py-5">
    <div class="container">
        <div class="row">
            <!-- Template Preview -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <img src="<?php echo htmlspecialchars($template['preview_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($template['name']); ?>">
                    <div class="card-body">
                        <h1 class="card-title h2"><?php echo htmlspecialchars($template['name']); ?></h1>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($template['description'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Purchase Box -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 mb-4">Template Details</h2>
                        <p class="mb-3"><strong>Price:</strong> $<?php echo number_format($template['price'], 2); ?></p>
                        <p class="mb-3"><strong>Category:</strong> <?php echo htmlspecialchars($template['category']); ?></p>
                        <p class="mb-3"><strong>Last Updated:</strong> <?php echo date('F j, Y', strtotime($template['updated_at'])); ?></p>
                        
                        <?php if ($isLoggedIn): ?>
                            <?php if (hasUserPurchased($template_id)): ?>
                                <a href="/templates/download.php?id=<?php echo $template_id; ?>" class="btn btn-success btn-lg w-100 mb-3">
                                    Download Template
                                </a>
                                <a href="/support/ticket/new?template_id=<?php echo $template_id; ?>" class="btn btn-outline-primary w-100">
                                    Get Support
                                </a>
                            <?php else: ?>
                                <form action="/gstore/cart_add.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $template_id; ?>">
                                    <input type="hidden" name="type" value="template">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        Add to Cart - $<?php echo number_format($template['price'], 2); ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-primary btn-lg w-100">
                                Login to Purchase
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Features -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h3 class="h5 mb-4">What's Included</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2">✓ Full Source Code</li>
                            <li class="mb-2">✓ Documentation</li>
                            <li class="mb-2">✓ 6 Months Support</li>
                            <li class="mb-2">✓ Free Updates</li>
                            <li class="mb-2">✓ Commercial License</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Features -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h3 mb-4">Template Features</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $features = explode("\n", $template['features']);
                                $half = ceil(count($features) / 2);
                                $firstHalf = array_slice($features, 0, $half);
                                ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($firstHalf as $feature): ?>
                                        <li class="mb-2">✓ <?php echo htmlspecialchars($feature); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <?php
                                $secondHalf = array_slice($features, $half);
                                ?>
                                <ul class="list-unstyled">
                                    <?php foreach ($secondHalf as $feature): ?>
                                        <li class="mb-2">✓ <?php echo htmlspecialchars($feature); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
