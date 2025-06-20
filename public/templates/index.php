<?php
require_once __DIR__ . '/../../app/bootstrap.php';

$page_title = 'Website Templates - ' . SITE_NAME;
$meta_description = 'Browse our collection of professional website templates for various industries.';

// Get categories
use App\Repositories\TemplateRepository;
$categoriesStmt = $pdo->query('SELECT * FROM template_categories ORDER BY name');
$categories = $categoriesStmt->fetchAll(PDO::FETCH_OBJ);

// Get selected category
$category = $_GET['category'] ?? null;

// Get templates
if ($category) {
    $templates = (new TemplateRepository())->getByCategory($category);
} else {
    $templates = (new TemplateRepository())->getAll();
}

include_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold">Website Templates</h1>
                <p class="lead">Professional templates for your next project</p>
                <div class="d-flex gap-2">
                    <a href="#templates" class="btn btn-light">Browse Templates</a>
                    <a href="/contact" class="btn btn-outline-light">Custom Development</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo asset('img/templates-hero.jpg'); ?>" alt="Templates" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Categories</h2>
            <?php if ($category): ?>
            <a href="/templates" class="btn btn-outline-primary">Show All</a>
            <?php endif; ?>
        </div>
        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="?category=<?php echo urlencode($cat->slug); ?>" 
                   class="card h-100 border-0 shadow-sm text-decoration-none <?php echo $category === $cat->slug ? 'bg-primary text-white' : ''; ?>">
                    <div class="card-body text-center">
                        <i class="fas fa-<?php echo $cat->slug === 'web-development' ? 'globe' : 
                            ($cat->slug === 'fintech' ? 'chart-line' : 
                            ($cat->slug === 'ecommerce' ? 'shopping-cart' : 
                            ($cat->slug === 'portfolio' ? 'user' : 
                            ($cat->slug === 'business' ? 'briefcase' : 'cogs')))); ?> fa-2x mb-2"></i>
                        <h3 class="h6 mb-0"><?php echo htmlspecialchars($cat->name); ?></h3>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Templates -->
<section id="templates" class="py-5 bg-light">
    <div class="container">
        <h2 class="h4 mb-4">
            <?php echo $category ? htmlspecialchars(ucfirst($category)) . ' Templates' : 'All Templates'; ?>
        </h2>
        <div class="row g-4">
            <?php foreach ($templates as $template): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <?php
                        $relPath = ltrim($template->preview_url, '/');
                        $absPath = __DIR__ . '/../../' . $relPath;
                        $imgSrc = file_exists($absPath) ? SITE_URL . '/' . $relPath : SITE_URL . '/assets/img/logo.png';
                    ?>
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($template->name); ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h3 class="h5 mb-0"><?php echo htmlspecialchars($template->name); ?></h3>
                            <span class="badge bg-primary">$<?php echo number_format($template->price, 2); ?></span>
                        </div>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($template->description); ?></p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex gap-2">
                            <a href="/templates/<?php echo $template->id; ?>" class="btn btn-primary w-100">
                                View Details
                            </a>
                            <button class="btn btn-outline-primary" 
                                    onclick="window.open('/templates/preview/<?php echo $template->id; ?>', '_blank')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-5">
    <div class="container">
        <h2 class="h4 text-center mb-5">Why Choose Our Templates?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                    <h3 class="h5">Responsive Design</h3>
                    <p class="text-muted">All templates are fully responsive and work perfectly on all devices.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-code fa-3x text-primary mb-3"></i>
                    <h3 class="h5">Clean Code</h3>
                    <p class="text-muted">Well-structured, documented code following best practices.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-life-ring fa-3x text-primary mb-3"></i>
                    <h3 class="h5">Premium Support</h3>
                    <p class="text-muted">Get expert support for any questions or customizations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="h4 text-center mb-5">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Can I customize the templates?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, all our templates are fully customizable. You can modify the design, colors, content, and functionality to match your needs.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                What's included in the download?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You'll receive all source files, including HTML, CSS, JavaScript, and any associated assets. Documentation is also included.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Do you offer refunds?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We offer a 30-day money-back guarantee if you're not satisfied with your purchase.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
