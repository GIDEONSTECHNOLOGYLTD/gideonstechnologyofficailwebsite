<?php
// Template management page for user's purchased templates

// Add necessary security checks
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Fetch user's templates from the database (this would be implemented in a controller)
$templates = isset($templates) ? $templates : [];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appName ?> - My Templates</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>My Purchased Templates</h1>
        <p class="lead">Manage and access all the website templates you've purchased</p>
        
        <?php if (empty($templates)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                You haven't purchased any templates yet. 
                <a href="/web-dev/templates" class="alert-link">Browse our templates gallery</a> to get started.
            </div>
        <?php else: ?>
            <div class="row mt-4">
                <?php foreach ($templates as $template): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="<?= isset($template['preview_image']) ? htmlspecialchars($template['preview_image']) : '/assets/img/template-placeholder.jpg' ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($template['name']) ?> preview">
                                <div class="position-absolute top-0 end-0 bg-success text-white m-2 px-2 py-1 rounded">
                                    <small>Purchased</small>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($template['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($template['description']) ?></p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="badge bg-primary">
                                        <?= isset($template['category']) ? htmlspecialchars($template['category']) : 'Website Template' ?>
                                    </span>
                                    <small class="text-muted">
                                        Purchased: <?= isset($template['purchase_date']) ? date('M d, Y', strtotime($template['purchase_date'])) : 'N/A' ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/web-dev/template/<?= $template['id'] ?>/details" class="btn btn-outline-primary">
                                        <i class="fas fa-info-circle me-1"></i> View Details
                                    </a>
                                    <a href="/web-dev/template/<?= $template['id'] ?>/download" class="btn btn-success">
                                        <i class="fas fa-download me-1"></i> Download Files
                                    </a>
                                    <?php if (isset($template['demo_url']) && $template['demo_url']): ?>
                                        <a href="<?= htmlspecialchars($template['demo_url']) ?>" 
                                           target="_blank" class="btn btn-outline-secondary">
                                            <i class="fas fa-external-link-alt me-1"></i> Live Demo
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="/web-dev/templates" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Browse More Templates
            </a>
            <a href="/user/dashboard" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>