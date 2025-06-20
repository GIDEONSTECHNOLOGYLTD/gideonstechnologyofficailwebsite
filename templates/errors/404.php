<?php
/**
 * 404 Error Page Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - <?= htmlspecialchars($appName ?? 'Gideon\'s Technology') ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .error-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .error-message {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .error-actions {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-content">
                <div class="error-code">404</div>
                <h1 class="error-title">Page Not Found</h1>
                <p class="error-message">
                    <?= htmlspecialchars($message ?? 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.') ?>
                </p>
                <p class="error-detail">
                    Requested URL: <code><?= htmlspecialchars($requestedPath ?? $_SERVER['REQUEST_URI']) ?></code>
                </p>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary">Go to Homepage</a>
                    <a href="/contact" class="btn btn-outline-secondary">Contact Support</a>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>