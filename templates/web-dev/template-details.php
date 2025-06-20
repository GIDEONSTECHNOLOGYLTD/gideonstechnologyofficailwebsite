<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - Template Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .preview-image {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            max-width: 100%;
            height: auto;
        }
        .price-tag {
            font-size: 2rem;
            color: #0d6efd;
        }
        .feature-list {
            margin-left: 1.5rem;
        }
        .feature-list li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/web-dev/templates">Templates</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($template['name']) ?></li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-md-6">
                <img src="/assets/img/templates/<?= htmlspecialchars($template['preview_image']) ?>" 
                     class="preview-image mb-4" 
                     alt="<?= htmlspecialchars($template['name']) ?>">
                
                <div class="d-grid gap-2">
                    <a href="<?= htmlspecialchars($template['demo_url']) ?>" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-desktop me-2"></i> View Live Demo
                    </a>
                </div>
            </div>
            
            <div class="col-md-6">
                <h1><?= htmlspecialchars($template['name']) ?></h1>
                <span class="badge bg-light text-dark mb-3"><?= htmlspecialchars($template['category']) ?></span>
                
                <p class="lead"><?= htmlspecialchars($template['description']) ?></p>
                
                <div class="price-tag mb-3">$<?= number_format($template['price'], 2) ?></div>
                
                <div class="d-grid gap-2 mb-4">
                    <a href="/web-dev/template/<?= $template['id'] ?>/purchase" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i> Purchase Template
                    </a>
                </div>
                
                <h4>Features</h4>
                <ul class="feature-list">
                    <?php foreach ($template['features'] as $feature): ?>
                        <li><i class="fas fa-check text-success me-2"></i> <?= htmlspecialchars($feature) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col">
                <h3>Template Details</h3>
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Technical Specifications</h5>
                        <ul>
                            <li>Responsive design</li>
                            <li>Cross-browser compatibility</li>
                            <li>Well-documented code</li>
                            <li>Regular updates</li>
                            <li>6 months support</li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>What's Included</h5>
                        <ul>
                            <li>HTML/CSS files</li>
                            <li>JavaScript files</li>
                            <li>Documentation</li>
                            <li>Sample content</li>
                            <li>Image assets</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col">
                <div class="d-flex justify-content-between">
                    <a href="/web-dev/templates" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Templates
                    </a>
                    <a href="/contact" class="btn btn-outline-primary">
                        <i class="fas fa-question-circle me-2"></i> Questions? Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>