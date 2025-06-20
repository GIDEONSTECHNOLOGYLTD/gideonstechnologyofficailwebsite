<?php
/**
 * Application Startup Script
 * 
 * This script runs necessary checks and starts the application locally
 */

echo "🚀 Starting Gideon's Technology Application\n\n";

// Check if database directory exists, if not create it
$dbDir = __DIR__ . '/database';
if (!file_exists($dbDir)) {
    echo "Creating database directory...\n";
    mkdir($dbDir, 0755, true);
}

// Check if .env file exists
if (!file_exists(__DIR__ . '/.env')) {
    echo "⚠️ .env file not found. Creating sample .env file...\n";
    $envContent = "APP_ENV=development\n" .
                 "APP_DEBUG=true\n" .
                 "APP_URL=http://localhost\n";
    file_put_contents(__DIR__ . '/.env', $envContent);
    echo "✅ Sample .env file created\n";
}

// Run setup if database doesn't exist
$dbPath = __DIR__ . '/database/gtech.db';
if (!file_exists($dbPath)) {
    echo "Database not found. Running setup...\n";
    include __DIR__ . '/setup.php';
}

// Run security checks
echo "\n";
include __DIR__ . '/security_check.php';

// Start local development server
echo "\n🌐 Starting development server...\n";

// Try different ports if the default one is in use
$ports = [8000, 8080, 3000, 5000, 8888];
$serverStarted = false;

foreach ($ports as $port) {
    echo "Attempting to start server on port $port...\n";
    
    // Check if port is already in use
    $connection = @fsockopen('localhost', $port);
    if (is_resource($connection)) {
        // Port is in use
        fclose($connection);
        echo "Port $port is already in use. Trying another port...\n";
        continue;
    }
    
    // Port is available
    echo "Server will be available at http://localhost:$port\n";
    echo "Press Ctrl+C to stop the server\n\n";

    // Construct the PHP server command
    $command = "php -S localhost:$port -t " . escapeshellarg(__DIR__);
    
    // On Windows systems, we need to use different command syntax
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        pclose(popen("start /B " . $command, "r"));
    } else {
        // On Unix systems, we can use exec
        echo shell_exec($command . " 2>&1");
    }
    
    $serverStarted = true;
    break;
}

if (!$serverStarted) {
    echo "❌ ERROR: Could not find an available port to start the server.\n";
    echo "Please ensure ports 8000, 8080, 3000, 5000, or 8888 are available and try again.\n";
    echo "Alternatively, manually start the server with:\n";
    echo "php -S localhost:<port> -t " . __DIR__ . "\n";
    exit(1);
}

// Add a debug output to check if the server is starting correctly
if (php_sapi_name() === 'cli-server') {
    echo "Development server started at http://localhost:8000\n";
    echo "Press Ctrl+C to stop the server\n";
}

// Make sure we're properly serving static files
if (php_sapi_name() === 'cli-server') {
    $file = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false; // Serve the requested file directly
    }
}