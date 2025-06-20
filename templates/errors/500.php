<?php
/**
 * 500 Error Page Template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - <?= htmlspecialchars($appName ?? 'Gideon\'s Technology') ?></title>
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
        .error-detail {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
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
                <div class="error-code">500</div>
                <h1 class="error-title">Server Error</h1>
                <p class="error-message">
                    <?= htmlspecialchars($message ?? 'An unexpected error occurred. Our technical team has been notified.') ?>
                </p>
                
                <?php if (isset($error) && !empty($error)): ?>
                <div class="error-detail">
                    <h3>Error Details</h3>
                    <p><strong>Message:</strong> <?= htmlspecialchars($error['message'] ?? 'Unknown error') ?></p>
                    <p><strong>File:</strong> <?= htmlspecialchars($error['file'] ?? 'Unknown file') ?></p>
                    <p><strong>Line:</strong> <?= htmlspecialchars($error['line'] ?? 'Unknown line') ?></p>
                    <?php if (isset($error['trace'])): ?>
                    <pre><?= htmlspecialchars($error['trace']) ?></pre>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
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