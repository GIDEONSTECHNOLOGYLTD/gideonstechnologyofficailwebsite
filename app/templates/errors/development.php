<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 40px; }
        .error-container { max-width: 800px; margin: 0 auto; }
        .error-details { background-color: #f8f9fa; border-radius: 5px; padding: 15px; }
        .error-stack { max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="alert alert-danger">
            <h2><i class="bi bi-exclamation-triangle-fill"></i> Application Error</h2>
            <p>An unexpected error has occurred in the application.</p>
        </div>
        
        <div class="error-details mb-4">
            <h4>Error Details</h4>
            <table class="table">
                <tr>
                    <th width="100">Type:</th>
                    <td><?= htmlspecialchars($error['type'] ?? 'Unknown') ?></td>
                </tr>
                <tr>
                    <th>Message:</th>
                    <td><?= htmlspecialchars($error['message'] ?? 'No message available') ?></td>
                </tr>
                <tr>
                    <th>File:</th>
                    <td><?= htmlspecialchars($error['file'] ?? 'Unknown') ?></td>
                </tr>
                <tr>
                    <th>Line:</th>
                    <td><?= htmlspecialchars($error['line'] ?? 'Unknown') ?></td>
                </tr>
            </table>
        </div>
        
        <?php if (isset($error['trace'])): ?>
        <div class="card mb-4">
            <div class="card-header">Stack Trace</div>
            <div class="card-body error-stack">
                <pre><?= htmlspecialchars($error['trace']) ?></pre>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="alert alert-info">
            <p><strong>Note:</strong> You are seeing this detailed error message because the application is in development mode. In production, users will see a generic error message.</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
