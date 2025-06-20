<?php
// Dashboard template for viewing purchased templates
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - My Templates</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .template-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .template-img {
            height: 180px;
            object-fit: cover;
        }
        .template-actions {
            display: flex;
            gap: 10px;
        }
        .template-actions .btn {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <h1>My Purchased Templates</h1>
        <p class="lead">Access and manage your website templates</p>
        
        <?php if (empty($purchasedTemplates)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                You haven't purchased any templates yet. 
                <a href="/web-dev/templates" class="alert-link">Browse our collection</a> to get started!
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($purchasedTemplates as $template): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card template-card">
                            <div class="position-relative">
                                <img src="/assets/img/templates/<?= htmlspecialchars($template['preview_image']) ?>" 
                                     class="card-img-top template-img" 
                                     alt="<?= htmlspecialchars($template['name']) ?>">
                                <div class="position-absolute top-0 end-0 bg-success text-white m-2 px-2 py-1 rounded">
                                    <small>Purchased</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($template['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($template['description']) ?></p>
                                <p class="text-muted small">
                                    <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($template['category']) ?> •
                                    <i class="fas fa-calendar me-1"></i> Purchased: <?= date('M d, Y', strtotime($template['purchase_date'])) ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="template-actions">
                                    <a href="/web-dev/template/<?= $template['id'] ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="/web-dev/template/<?= $template['id'] ?>/download" class="btn btn-success">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <?php if (isset($template['demo_url'])): ?>
                                    <a href="<?= htmlspecialchars($template['demo_url']) ?>" class="btn btn-outline-secondary" target="_blank">
                                        <i class="fas fa-desktop"></i> Demo
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="/dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
            <a href="/web-dev/templates" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Browse More Templates
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>