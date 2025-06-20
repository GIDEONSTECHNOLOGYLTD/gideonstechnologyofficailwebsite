<?php
/**
 * Gideon's Technology - Router Script
 * 
 * This file routes requests to the front controller (public/index.php)
 * for the built-in PHP server, while serving static files directly.
 */

// Set a flag to track if the request has been handled by Slim
$handled = false;

// Get the requested file
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the file exists as is, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri) && is_file(__DIR__ . '/public' . $uri)) {
    // Determine the file's MIME type
    $extension = pathinfo($uri, PATHINFO_EXTENSION);
    switch ($extension) {
        case 'css':
            $contentType = 'text/css';
            break;
        case 'js':
            $contentType = 'application/javascript';
            break;
        case 'jpg':
        case 'jpeg':
            $contentType = 'image/jpeg';
            break;
        case 'png':
            $contentType = 'image/png';
            break;
        case 'gif':
            $contentType = 'image/gif';
            break;
        case 'svg':
            $contentType = 'image/svg+xml';
            break;
        case 'pdf':
            $contentType = 'application/pdf';
            break;
        default:
            $contentType = 'text/html';
    }
    
    header('Content-Type: ' . $contentType);
    readfile(__DIR__ . '/public' . $uri);
    return true;
}

// Otherwise, route the request to index.php
require __DIR__ . '/public/index.php';