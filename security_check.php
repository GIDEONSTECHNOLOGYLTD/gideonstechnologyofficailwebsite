<?php
echo "🔒 Running security checks...\n\n";

// Load environment variables if needed
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

// Check .env file
echo "Checking .env file...\n";
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    if (strpos($env, 'APP_DEBUG=true') !== false) {
        echo "❌ WARNING: Debug mode is enabled in .env\n";
    } else {
        echo "✅ Debug mode is disabled\n";
    }
    
    if (strpos($env, 'APP_ENV=production') !== false) {
        echo "✅ Production environment is set\n";
    } else {
        echo "❌ WARNING: Not in production environment\n";
    }
} else {
    echo "❌ ERROR: .env file not found\n";
}

// Check file permissions
echo "\nChecking file permissions...\n";
$sensitiveFiles = [
    '.env',
    'database/gtech.db',
    'storage/logs',
    'vendor'
];

foreach ($sensitiveFiles as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $worldWritable = $perms & 0x0002;
        if ($worldWritable) {
            echo "❌ WARNING: $file is world-writable\n";
        } else {
            echo "✅ $file permissions are secure\n";
        }
    }
}

// Check database connection
echo "\nChecking database connection...\n";
try {
    $dbPath = __DIR__ . '/database/gtech.db';
    
    // Check if database file exists
    if (!file_exists($dbPath)) {
        echo "❌ Database file not found at: $dbPath\n";
    } else {
        $pdo = new PDO(
            'sqlite:' . $dbPath,
            null,
            null,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        echo "✅ Database connection successful\n";
        
        // Check if tables exist
        $tables = ['users', 'products', 'orders'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
            if ($stmt->fetch()) {
                echo "✅ Table '$table' exists\n";
            } else {
                echo "❌ Table '$table' is missing\n";
            }
        }
    }
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Check web security headers if site is accessible
echo "\nChecking security headers...\n";
$siteUrl = getenv('APP_URL') ?: 'https://gideonstechnology.com';
try {
    $headers = @get_headers($siteUrl, 1);
    if ($headers) {
        $securityHeaders = [
            'X-Content-Type-Options',
            'X-Frame-Options',
            'X-XSS-Protection',
            'Content-Security-Policy',
            'Strict-Transport-Security'
        ];

        foreach ($securityHeaders as $header) {
            if (isset($headers[$header])) {
                echo "✅ $header is set\n";
            } else {
                echo "❌ $header is missing\n";
            }
        }
    } else {
        echo "❌ Could not check headers - site may be unavailable\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking headers: " . $e->getMessage() . "\n";
}

echo "\n🔍 Security check completed!\n";
