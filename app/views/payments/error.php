<?php require_once APP_PATH . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 64px;"></i>
                    </div>
                    <h2 class="card-title mb-4">Payment Failed</h2>
                    <p class="card-text mb-4">We're sorry, but there was a problem processing your payment.</p>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger mb-4">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php endif; ?>

                    <div class="text-center">
                        <a href="/payment/<?= htmlspecialchars($order['id']) ?>" class="btn btn-primary me-2">Try Again</a>
                        <a href="/contact" class="btn btn-outline-primary">Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
