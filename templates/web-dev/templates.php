<!DOCTYPE html>
<html>
<head>
    <title><?= $appName ?> - Website Templates</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .template-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .price-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .category-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 10px;
        }
        .template-img {
            height: 220px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <h1>Website Templates</h1>
        <p class="lead">Browse our collection of professional website templates for your business or project</p>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <form action="/web-dev/templates/search" method="GET" class="d-flex">
                    <input type="text" name="query" class="form-control me-2" placeholder="Search templates...">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end mt-3 mt-md-0">
                    <div class="btn-group">
                        <a href="/web-dev/templates" class="btn <?= !isset($selectedCategory) ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
                        <a href="/web-dev/templates/category/business" class="btn <?= isset($selectedCategory) && $selectedCategory == 'business' ? 'btn-primary' : 'btn-outline-primary' ?>">Business</a>
                        <a href="/web-dev/templates/category/e-commerce" class="btn <?= isset($selectedCategory) && $selectedCategory == 'e-commerce' ? 'btn-primary' : 'btn-outline-primary' ?>">E-Commerce</a>
                        <a href="/web-dev/templates/category/portfolio" class="btn <?= isset($selectedCategory) && $selectedCategory == 'portfolio' ? 'btn-primary' : 'btn-outline-primary' ?>">Portfolio</a>
                        <a href="/web-dev/templates/category/education" class="btn <?= isset($selectedCategory) && $selectedCategory == 'education' ? 'btn-primary' : 'btn-outline-primary' ?>">Education</a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (empty($templates)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No templates found. Please try a different search or category.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($templates as $template): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 template-card">
                            <div class="position-relative">
                                <img src="/assets/img/templates/<?= htmlspecialchars($template['preview_image']) ?>" 
                                     class="card-img-top template-img" 
                                     alt="<?= htmlspecialchars($template['name']) ?>">
                                <div class="price-tag">$<?= number_format($template['price'], 2) ?></div>
                            </div>
                            <div class="card-body">
                                <span class="category-badge"><?= htmlspecialchars($template['category']) ?></span>
                                <h5 class="card-title"><?= htmlspecialchars($template['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($template['description']) ?></p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="/web-dev/template/<?= $template['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-info-circle me-1"></i> View Details
                                    </a>
                                    <a href="<?= htmlspecialchars($template['demo_url']) ?>" target="_blank" class="btn btn-outline-secondary">
                                        <i class="fas fa-desktop me-1"></i> Live Demo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="/user/dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
            <a href="/web-dev/templates/purchased" class="btn btn-success">
                <i class="fas fa-shopping-bag me-1"></i> My Purchased Templates
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>