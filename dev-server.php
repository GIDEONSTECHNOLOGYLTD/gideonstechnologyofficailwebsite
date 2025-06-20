<?php
/**
 * Enhanced development server for Gideon's Technology
 * - Adds proper CORS headers
 * - Disables browser caching
 * - Provides detailed error reporting
 */

// Set error reporting to maximum for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle CORS for cross-browser compatibility
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

// Disable browser caching for development
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly (document root is already set to public)
if ($uri !== '/' && file_exists($_SERVER['DOCUMENT_ROOT'] . $uri) && is_file($_SERVER['DOCUMENT_ROOT'] . $uri)) {
    $path = $_SERVER['DOCUMENT_ROOT'] . $uri;
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    
    // Set content type based on file extension
    switch ($ext) {
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        case 'json':
            header('Content-Type: application/json');
            break;
        case 'svg':
            header('Content-Type: image/svg+xml');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        case 'pdf':
            header('Content-Type: application/pdf');
            break;
    }
    
    readfile($path);
    exit;
}

// For all other requests, include the main index.php file
require_once __DIR__ . '/public/index.php';
