#!/usr/bin/env php
<?php
/**
 * Simple server startup script that tries multiple ports
 */

$ports = [8080, 3000, 5000, 8888, 9000];
$serverStarted = false;

echo "🚀 Starting Gideon's Technology Application\n\n";

foreach ($ports as $port) {
    echo "Attempting to start server on port $port...\n";
    
    // Check if port is available
    $connection = @fsockopen('localhost', $port, $errno, $errstr, 1);
    if (is_resource($connection)) {
        fclose($connection);
        echo "Port $port is already in use. Trying another port...\n";
        continue;
    }
    
    // Port is available
    echo "✅ Server is starting at http://localhost:$port\n";
    echo "Press Ctrl+C to stop the server\n\n";
    
    // Start the server on the available port
    passthru("php -S localhost:$port");
    
    $serverStarted = true;
    break;
}

if (!$serverStarted) {
    echo "❌ ERROR: Could not find an available port to start the server.\n";
    echo "Please manually check which processes are using your ports with:\n";
    echo "lsof -i -P | grep LISTEN\n";
    exit(1);
}

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri) && is_file(__DIR__.'/public'.$uri)) {
    $path = __DIR__.'/public'.$uri;
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    
    // Set content type based on file extension
    switch ($ext) {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        case 'ico':
            header('Content-Type: image/x-icon');
            break;
    }
    
    readfile($path);
    return;
}

require_once __DIR__.'/public/index.php';
