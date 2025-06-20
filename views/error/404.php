<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1 text-muted">404</h1>
            <h2 class="mb-4">Page Not Found</h2>
            <p class="lead mb-5">
                The page you are looking for might have been removed, had its name changed,
                or is temporarily unavailable.
            </p>
            <div>
                <a href="/" class="btn btn-primary">Go to Homepage</a>
                <button class="btn btn-secondary ms-2" onclick="window.history.back();">Go Back</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>