<?php
header('Content-Type: text/plain');

// Basic server info
echo "Server Info:\n";
echo "============\n";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n\n";

// Check if we can write to directories
echo "Directory Permissions:\n";
echo "====================\n";
$dirs = array('.', 'public_html', 'includes');
foreach ($dirs as $dir) {
    echo "$dir: " . (is_writable($dir) ? "Writable" : "Not writable") . "\n";
}

// Check PHP configuration
echo "\nPHP Configuration:\n";
echo "=================\n";
echo "open_basedir: " . ini_get('open_basedir') . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "error_reporting: " . ini_get('error_reporting') . "\n";

// Try to create a test file
echo "\nFile Creation Test:\n";
echo "=================\n";
$test_file = 'test_write.txt';
if (@file_put_contents($test_file, 'test')) {
    echo "Successfully created test file\n";
    unlink($test_file);
} else {
    echo "Failed to create test file\n";
}

// Check DNS resolution
echo "\nDNS Resolution:\n";
echo "==============\n";
$domain = 'gideonstechnology.com';
$ip = gethostbyname($domain);
echo "$domain resolves to: $ip\n";

// Check if we can connect to the database
echo "\nDatabase Connection:\n";
echo "==================\n";
try {
    $db = new PDO(
        "mysql:host=localhost;dbname=gideonst_db",
        "gideonst_user",
        "your_password_here"
    );
    echo "Database connection successful\n";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
