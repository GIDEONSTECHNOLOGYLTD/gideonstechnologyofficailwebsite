<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Website Templates</h1>
    
    <!-- Categories Filter -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="list-group">
                            <a href="/web-dev/templates" class="list-group-item list-group-item-action <?php echo empty($selectedCategory) ? 'active' : ''; ?>">
                                All Templates
                            </a>
                            <?php foreach ($categories as $category): ?>
                                <a href="/web-dev/templates/category/<?php echo urlencode($category); ?>" 
                                   class="list-group-item list-group-item-action <?php echo $category === $selectedCategory ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Featured Templates -->
        <div class="col-md-9">
            <div class="row">
                <?php foreach ($templates as $template): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($template['is_featured']): ?>
                                <div class="position-absolute top-0 start-0 p-2">
                                    <span class="badge bg-success">Featured</span>
                                </div>
                            <?php endif; ?>
                            
                            <img src="/public/images/<?php echo htmlspecialchars($template['preview_image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($template['name']); ?> preview">
                            
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($template['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($template['description']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary h5 mb-0">
                                        $<?php echo number_format($template['price'], 2); ?>
                                    </span>
                                    <span class="badge bg-info">
                                        <?php echo $template['purchases']; ?> purchases
                                    </span>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="/web-dev/template/<?php echo $template['id']; ?>" 
                                       class="btn btn-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
