<?php
/**
 * Slim 4 Application Startup Script
 * 
 * This script starts a local PHP development server for your Slim 4 application.
 * Run this script with PHP to start your application on http://localhost:8080
 */

// Configuration
$host = 'localhost';
$port = 8080;
$publicDir = __DIR__ . '/public';

// Display startup banner
echo "\n=== Gideon's Technology - Slim 4 Application ===\n\n";
echo "Starting development server at http://{$host}:{$port}\n";
echo "Document root: {$publicDir}\n";
echo "Press Ctrl+C to stop the server\n\n";

// Verify that the public directory exists
if (!is_dir($publicDir)) {
    echo "Error: Public directory not found at {$publicDir}\n";
    exit(1);
}

// Check if index.php exists in the public directory
if (!file_exists($publicDir . '/index.php')) {
    echo "Error: index.php not found in {$publicDir}\n";
    exit(1);
}

// Start the PHP development server
$command = sprintf('php -S %s:%d -t %s', $host, $port, escapeshellarg($publicDir));

// On Windows, we need to use a different approach
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo "Starting server on Windows...\n";
    system($command);
} else {
    // On Unix-like systems, we can use pcntl_fork for a cleaner output
    echo "Starting server...\n";
    
    // Execute the command
    passthru($command);
}
