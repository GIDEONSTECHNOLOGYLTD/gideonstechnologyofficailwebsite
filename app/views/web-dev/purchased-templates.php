<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">My Purchased Templates</h1>
    
    <?php if (empty($templates)): ?>
        <div class="alert alert-info">
            You haven't purchased any templates yet. Browse our collection of templates to get started!
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($templates as $template): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="/public/images/<?php echo htmlspecialchars($template['preview_image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($template['name']); ?> preview">
                        
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($template['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($template['description']); ?></p>
                            
                            <div class="mt-3">
                                <div class="btn-group w-100" role="group">
                                    <a href="/web-dev/template/<?php echo $template['id']; ?>" 
                                       class="btn btn-primary w-100 mb-2">
                                        View Template
                                    </a>
                                    
                                    <a href="<?php echo htmlspecialchars($template['demo_url']); ?>" 
                                       target="_blank" 
                                       class="btn btn-secondary w-100 mb-2">
                                        View Live Demo
                                    </a>
                                    
                                    <a href="/web-dev/template/<?php echo $template['id']; ?>/download" 
                                       class="btn btn-success w-100">
                                        Download Files
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
