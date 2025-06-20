<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<?php
use App\Core\Security;

$security = Security::getInstance();
$csrfToken = $security->generateCSRFToken();
?>

<div class="container mt-5">
    <div class="row">
        <!-- Template Preview -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($template['name']); ?></h1>
                    <p class="card-text"><?php echo htmlspecialchars($template['description']); ?></p>
                    
                    <div class="mt-4">
                        <h5>Preview</h5>
                        <img src="/public/images/<?php echo htmlspecialchars($template['preview_image']); ?>" 
                             class="img-fluid rounded" 
                             alt="<?php echo htmlspecialchars($template['name']); ?> preview">
                    </div>
                    
                    <div class="mt-4">
                        <h5>Features</h5>
                        <ul class="list-group">
                            <?php foreach (explode(',', $template['features']) as $feature): ?>
                                <li class="list-group-item"><?php echo trim(htmlspecialchars($feature)); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Customization Options -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Customization Options</h5>
                    
                    <?php 
                    $options = json_decode($template['customization_options'], true);
                    foreach ($options as $type => $optionsList): 
                        if (empty($optionsList)) continue;
                    ?>
                        <div class="mt-3">
                            <h6><?php echo ucfirst($type); ?></h6>
                            <div class="row">
                                <?php foreach ($optionsList as $option): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="<?php echo $option; ?>" 
                                                   name="customization[<?php echo $type; ?>][]" 
                                                   value="<?php echo $option; ?>">
                                            <label class="form-check-label" 
                                                   for="<?php echo $option; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $option)); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Template Details -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Template Details</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Price:</strong> $<?php echo number_format($template['price'], 2); ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Category:</strong> <?php echo htmlspecialchars($template['category']); ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Subcategory:</strong> <?php echo htmlspecialchars($template['subcategory']); ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Purchases:</strong> <?php echo $template['purchases']; ?>
                        </li>
                    </ul>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="/web-dev/template/<?php echo $template['id']; ?>/purchase" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    Purchase Now - $<?php echo number_format($template['price'], 2); ?>
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="mt-3">
                            <a href="/auth/login" class="btn btn-primary btn-lg w-100">
                                Login to Purchase
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Demo Link -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Live Demo</h5>
                    <a href="<?php echo htmlspecialchars($template['demo_url']); ?>" 
                       target="_blank" 
                       class="btn btn-secondary w-100">
                        View Live Demo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="/web-dev/template/<?php echo $template['id']; ?>/purchase"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Add client-side validation
            const options = form.querySelectorAll('input[type="checkbox"]');
            for (const option of options) {
                if (!option.checked) {
                    e.preventDefault();
                    alert('Please select all customization options');
                    return;
                }
            }
        });
    }
});
</script>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
