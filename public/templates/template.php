<?php
require_once __DIR__ . '/../../app/bootstrap.php';

// Get template ID from URL
$template_id = $_GET['id'] ?? null;
if (!$template_id) {
    redirect('/templates');
}

// Get template details
$stmt = $pdo->prepare('SELECT t.*, c.name as category_name 
                       FROM templates t 
                       JOIN template_categories c ON t.category = c.slug 
                       WHERE t.id = ?');
$stmt->execute([$template_id]);
$template = $stmt->fetch();

if (!$template) {
    redirect('/templates');
}

$page_title = $template->name . ' - Templates - ' . SITE_NAME;
$meta_description = $template->description;

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Template Details -->
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/templates">Templates</a></li>
            <li class="breadcrumb-item">
                <a href="/templates?category=<?php echo urlencode($template->category); ?>">
                    <?php echo htmlspecialchars($template->category_name); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo htmlspecialchars($template->name); ?>
            </li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Template Preview -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <?php
                    $relPath = ltrim($template->preview_url, '/');
                    $absPath = __DIR__ . '/../../' . $relPath;
                    $imgSrc = file_exists($absPath) ? SITE_URL . '/' . $relPath : SITE_URL . '/assets/img/logo.png';
                ?>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($template->name); ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h3 mb-1"><?php echo htmlspecialchars($template->name); ?></h1>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($template->category_name); ?></span>
                        </div>
                        <div class="text-end">
                            <div class="h3 mb-0 text-primary">$<?php echo number_format($template->price, 2); ?></div>
                            <small class="text-muted">One-time payment</small>
                        </div>
                    </div>

                    <h2 class="h5 mb-3">Description</h2>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($template->description)); ?></p>

                    <h2 class="h5 mb-3">Features</h2>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Responsive Design
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Cross-browser Compatible
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Well-documented Code
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Free Updates
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Premium Support
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Easy Customization
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Detailed Documentation
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i> Source Files Included
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-4">Purchase Template</h2>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Template Price</span>
                            <span>$<?php echo number_format($template->price, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Support Duration</span>
                            <span>6 months</span>
                        </div>
                    </div>
                    <form action="/templates/purchase" method="POST">
                        <input type="hidden" name="template_id" value="<?php echo $template->id; ?>">
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            Purchase Now
                        </button>
                        <button type="button" class="btn btn-outline-primary w-100 mb-3" 
                                onclick="window.open('/templates/preview/<?php echo $template->id; ?>', '_blank')">
                            Live Preview
                        </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <h3 class="h6 mb-3">Need Help?</h3>
                        <a href="/contact" class="btn btn-outline-secondary btn-sm">Contact Support</a>
                    </div>
                </div>
            </div>

            <!-- Requirements -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Requirements</h2>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-code me-2"></i> HTML5
                        </li>
                        <li class="mb-2">
                            <i class="fab fa-css3 me-2"></i> CSS3
                        </li>
                        <li class="mb-2">
                            <i class="fab fa-js me-2"></i> JavaScript
                        </li>
                        <li>
                            <i class="fab fa-php me-2"></i> PHP 7.4+
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
