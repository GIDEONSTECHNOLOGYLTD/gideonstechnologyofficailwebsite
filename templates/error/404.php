<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #d9534f; }
        p { color: #666; }
        .btn { display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; margin-top: 20px; }
        .error-code { color: #999; font-size: 16px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Page Not Found</h1>
        <p><?= $message ?? 'The page you are looking for does not exist or has been moved.' ?></p>
        <a href="/" class="btn">Go to Homepage</a>
        <div class="error-code">Error Code: 404</div>
    </div>
</body>
</html>