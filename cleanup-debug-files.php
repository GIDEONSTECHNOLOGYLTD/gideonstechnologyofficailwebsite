<?php
/**
 * Gideons Technology - Debug Files Cleanup Script
 * 
 * This script safely backs up unnecessary debug, test, and standalone files
 * to a backup directory and then removes them from the public folder.
 * 
 * IMPORTANT: This script should be run from the root directory of the project.
 */

// Define the source directory (public)
$sourceDir = __DIR__ . '/public';

// Define the backup directory
$backupDir = __DIR__ . '/backup/debug_files_' . date('Ymd_His');

// Create backup directory if it doesn't exist
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "Created backup directory: $backupDir\n";
}

// Define patterns for files to clean up
$patterns = [
    // Debug files
    'debug*.php',
    'error-debug.php',
    'main-debug.php',
    'route-debug.php',
    'simple-debug.php',
    'trace-debug.php',
    
    // Test files
    '*test*.php',
    'api-test.html',
    
    // Direct files
    'direct-*.php',
    
    // Fixed files
    'fixed-*.php',
    'fixed_*.php',
    
    // Standalone files
    '*standalone*.php',
    
    // Simple/minimal files
    'simple-*.php',
    'minimal-*.php',
    'simplified-*.php',
    
    // Multiple versions
    '*-v2.php',
    '*-v3.php',
    
    // Backup files
    '*.bak',
    '*.bak2',
    'index.php.bak*',
    'router.php.bak*',
    'index 2.php',
    
    // Working copies
    'working-*.php'
];

// Files to preserve - NEVER delete these
$preserveFiles = [
    'index.php',
    'gstore.php',
    'gtech.php',
    '.htaccess',
    'login.php',
    'logout.php',
    'register.php',
    'contact.php',
    'about.php',
    'services.php',
    'fb-domain-verification.php',
    'include-structured-data.php'
];

// Collect all files to process
$allFiles = [];
foreach ($patterns as $pattern) {
    $files = glob($sourceDir . '/' . $pattern);
    $allFiles = array_merge($allFiles, $files);
}

// Deduplicate the file list
$allFiles = array_unique($allFiles);

// Process each file
$count = 0;
$total = count($allFiles);

echo "Found $total potential files to clean up.\n";
echo "Starting backup and cleanup process...\n\n";

foreach ($allFiles as $file) {
    $basename = basename($file);
    
    // Skip preserved files
    if (in_array($basename, $preserveFiles)) {
        echo "PRESERVING: $basename (in preserve list)\n";
        continue;
    }
    
    // Backup the file
    $backupFile = $backupDir . '/' . $basename;
    if (copy($file, $backupFile)) {
        echo "Backed up: $basename\n";
        
        // Remove the original file after successful backup
        if (unlink($file)) {
            echo "Removed: $basename\n";
            $count++;
        } else {
            echo "WARNING: Failed to remove $basename\n";
        }
    } else {
        echo "WARNING: Failed to backup $basename\n";
    }
}

echo "\nCleanup complete!\n";
echo "Removed $count of $total files.\n";
echo "All files are backed up to: $backupDir\n";
echo "\nIf you need to restore any files, you can find them in the backup directory.\n";
?>
