<?php
/**
 * Generic Error Page Template
 * Used for all error types when a specific template isn't available
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Error') ?> - <?= htmlspecialchars($appName ?? 'Gideon\'s Technology') ?></title>
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
        .debug-info {
            margin-top: 3rem;
            text-align: left;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
            font-family: monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-content">
                <div class="error-code"><?= isset($statusCode) ? htmlspecialchars($statusCode) : '500' ?></div>
                <h1 class="error-title"><?= htmlspecialchars($title ?? 'Server Error') ?></h1>
                <p class="error-message">
                    <?= htmlspecialchars($message ?? 'An unexpected error occurred. Please try again later.') ?>
                </p>
                <div class="error-actions">
                    <a href="/" class="btn btn-primary">Go to Homepage</a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary ml-2">Go Back</a>
                </div>
                
                <?php if (isset($debug) && is_array($debug)): ?>
                <div class="debug-info">
                    <h3>Debug Information</h3>
                    <p><strong>Message:</strong> <?= htmlspecialchars($debug['message'] ?? '') ?></p>
                    <p><strong>File:</strong> <?= htmlspecialchars($debug['file'] ?? '') ?></p>
                    <p><strong>Line:</strong> <?= htmlspecialchars($debug['line'] ?? '') ?></p>
                    
                    <?php if (isset($debug['trace']) && is_array($debug['trace'])): ?>
                    <h4>Stack Trace:</h4>
                    <pre><?= implode("\n", array_map('htmlspecialchars', $debug['trace'])) ?></pre>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
