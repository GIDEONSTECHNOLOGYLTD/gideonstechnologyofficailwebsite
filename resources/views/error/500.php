<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger mb-4"></i>
                    <h2>Something went wrong</h2>
                    <p class="lead text-muted">We're sorry, but we're experiencing some technical difficulties.</p>
                    <?php if (isset($error) && !empty($error)): ?>
                        <div class="alert alert-danger mt-3">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-4">
                        <a href="/" class="btn btn-primary">Go to Homepage</a>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Error Code: 500</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
