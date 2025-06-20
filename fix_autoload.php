<?php

/**
 * This script helps identify and fix autoloading issues by ensuring
 * proper directory structure and case sensitivity for PSR-4 autoloading.
 */

// Define the base directory
$baseDir = __DIR__;

// Define the directories that should be properly capitalized
$directories = [
    'app/Controllers',
    'app/Models',
    'app/Core',
    'app/Middleware',
    'app/Services',
    'app/Providers',
    'app/Repositories',
    'app/Utilities'
];

// Function to ensure directory exists with proper capitalization
function ensureDirectory($baseDir, $path) {
    $fullPath = $baseDir . '/' . $path;
    
    if (!file_exists($fullPath)) {
        echo "Creating directory: {$path}\n";
        mkdir($fullPath, 0755, true);
        return;
    }
    
    // Directory exists, check if it has the correct capitalization
    $parts = explode('/', $path);
    $currentPath = $baseDir;
    
    foreach ($parts as $part) {
        $entries = scandir($currentPath);
        $found = false;
        
        foreach ($entries as $entry) {
            if (strcasecmp($entry, $part) === 0 && $entry !== $part) {
                echo "Directory capitalization mismatch: {$currentPath}/{$entry} should be {$currentPath}/{$part}\n";
                // In a real fix, we would rename the directory here
                // But for safety, we're just reporting the issue
            } elseif ($entry === $part) {
                $found = true;
            }
        }
        
        if (!$found) {
            echo "Directory not found: {$currentPath}/{$part}\n";
        }
        
        $currentPath .= '/' . $part;
    }
}

// Check and fix each directory
echo "Checking directory structure for PSR-4 autoloading...\n";
foreach ($directories as $dir) {
    ensureDirectory($baseDir, $dir);
}

echo "\nChecking for controller files in lowercase directories...\n";

// Check if controllers exist in lowercase directories but not in proper case directories
if (is_dir($baseDir . '/app/controllers')) {
    $files = scandir($baseDir . '/app/controllers');
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $sourcePath = $baseDir . '/app/controllers/' . $file;
        $targetPath = $baseDir . '/app/Controllers/' . $file;
        
        if (is_file($sourcePath) && !file_exists($targetPath)) {
            echo "Controller file exists in lowercase directory but not in proper case directory: {$file}\n";
            // In a real fix, we would copy the file here
            // But for safety, we're just reporting the issue
        }
    }
}

echo "\nChecking for model files in lowercase directories...\n";

// Check if models exist in lowercase directories but not in proper case directories
if (is_dir($baseDir . '/app/models')) {
    $files = scandir($baseDir . '/app/models');
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $sourcePath = $baseDir . '/app/models/' . $file;
        $targetPath = $baseDir . '/app/Models/' . $file;
        
        if (is_file($sourcePath) && !file_exists($targetPath)) {
            echo "Model file exists in lowercase directory but not in proper case directory: {$file}\n";
            // In a real fix, we would copy the file here
            // But for safety, we're just reporting the issue
        }
    }
}

echo "\nDirectory structure check complete.\n";
echo "To fix autoloading issues, ensure all files are in properly capitalized directories\n";
echo "and run 'composer dump-autoload' to regenerate the autoloader.\n";
