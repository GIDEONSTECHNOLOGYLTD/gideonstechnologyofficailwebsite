<?php
echo "🔍 Verifying website...\n\n";

$base_url = "https://gideonstechnology.com";
$endpoints = [
    "/" => "Home Page",
    "/login.php" => "Login Page",
    "/register.php" => "Register Page",
    "/about.php" => "About Page",
    "/contact.php" => "Contact Page",
    "/services/" => "Services Page"
];

// Check if pages are accessible
foreach ($endpoints as $path => $name) {
    echo "Checking $name... ";
    $ch = curl_init($base_url . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($status == 200) {
        echo "✅ OK\n";
    } else {
        echo "❌ Failed (Status: $status)\n";
    }
}

// Check database connection
echo "\nChecking database connection... ";
try {
    // Load database credentials from a secure file
    if (file_exists('.env')) {
        $envVars = parse_ini_file('.env');
        $db_host = $envVars['DB_HOST'] ?? 'localhost';
        $db_name = $envVars['DB_NAME'] ?? '';
        $db_user = $envVars['DB_USER'] ?? '';
        $db_pass = $envVars['DB_PASS'] ?? '';
    } else {
        echo "❌ .env file not found. Cannot access database credentials securely.\n";
        exit(1);
    }
    
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name",
        $db_user,
        $db_pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected\n";
    
    // Check essential tables
    $tables = ['users', 'products', 'services', 'orders'];
    foreach ($tables as $table) {
        echo "Checking table '$table'... ";
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "✅ Found ($count records)\n";
        } else {
            echo "❌ Missing\n";
        }
    }
} catch (PDOException $e) {
    echo "❌ Failed: " . $e->getMessage() . "\n";
}

// Check file permissions
echo "\nChecking file permissions...\n";
$critical_files = [
    'index.php',
    'config.php',
    '.htaccess',
    'includes/Database.php'
];

foreach ($critical_files as $file) {
    echo "Checking '$file'... ";
    if (file_exists($file)) {
        $perms = fileperms($file);
        $is_writable = is_writable($file);
        echo ($is_writable ? "⚠️ Writable" : "✅ Protected") . "\n";
    } else {
        echo "❌ Not found\n";
    }
}

// Check security headers
echo "\nChecking security headers...\n";
$ch = curl_init($base_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$security_headers = [
    'X-Content-Type-Options',
    'X-Frame-Options',
    'X-XSS-Protection',
    'Content-Security-Policy',
    'Strict-Transport-Security'
];

foreach ($security_headers as $header) {
    echo "Checking '$header'... ";
    if (stripos($response, $header . ':') !== false) {
        echo "✅ Present\n";
    } else {
        echo "❌ Missing\n";
    }
}

echo "\n✅ Verification complete!\n";
