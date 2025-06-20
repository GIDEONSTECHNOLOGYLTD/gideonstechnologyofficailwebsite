<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="m-0">Error: <?= htmlspecialchars($code) ?></h4>
                </div>
                <div class="card-body text-center py-5">
                    <h2 class="mb-4">
                        <?php if ($code == 404): ?>
                            <i class="fas fa-question-circle text-muted mb-3 d-block" style="font-size: 4rem;"></i>
                            Page Not Found
                        <?php elseif ($code == 403): ?>
                            <i class="fas fa-lock text-muted mb-3 d-block" style="font-size: 4rem;"></i>
                            Access Denied
                        <?php elseif ($code == 500): ?>
                            <i class="fas fa-exclamation-triangle text-muted mb-3 d-block" style="font-size: 4rem;"></i>
                            Server Error
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle text-muted mb-3 d-block" style="font-size: 4rem;"></i>
                            An Error Occurred
                        <?php endif; ?>
                    </h2>
                    
                    <p class="lead mb-4">
                        <?= htmlspecialchars($message) ?>
                    </p>
                    
                    <div class="mt-5">
                        <a href="/" class="btn btn-primary">Go to Homepage</a>
                        <button class="btn btn-secondary ms-2" onclick="window.history.back();">Go Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>