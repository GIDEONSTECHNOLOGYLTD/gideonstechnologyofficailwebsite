<?php
/**
 * Slim 4 Application Optimization Script
 * 
 * This script applies all the optimization changes to the application structure
 * to ensure consistent routing, error handling, and middleware configuration.
 */

// Set the base directory
define('BASE_DIR', __DIR__);

// Function to create directory if it doesn't exist
function createDirectoryIfNotExists($dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// Function to backup a file before replacing it
function backupFile($file) {
    if (file_exists($file)) {
        $backupFile = $file . '.bak.' . date('YmdHis');
        copy($file, $backupFile);
        echo "Backed up $file to $backupFile\n";
        return true;
    }
    return false;
}

// Function to replace a file with a new version
function replaceFile($oldFile, $newFile) {
    if (file_exists($newFile)) {
        backupFile($oldFile);
        rename($newFile, $oldFile);
        echo "Replaced $oldFile with $newFile\n";
        return true;
    }
    return false;
}

// Create necessary directories
echo "\n=== Creating necessary directories ===\n";
$createDirList = [
    BASE_DIR . '/app/config',
    BASE_DIR . '/app/templates/errors',
    BASE_DIR . '/app/middleware',
    BASE_DIR . '/app/repositories',
    BASE_DIR . '/app/services',
    BASE_DIR . '/logs',
    BASE_DIR . '/cache',
    BASE_DIR . '/cache/twig',
];

foreach ($createDirList as $dir) {
    createDirectoryIfNotExists($dir);
}

// Apply file replacements
echo "\n=== Applying file replacements ===\n";
$replaceList = [
    BASE_DIR . '/public/index.php' => BASE_DIR . '/public/index.php.new',
    BASE_DIR . '/routes/routes.php' => BASE_DIR . '/routes/routes.php.new',
];

foreach ($replaceList as $oldFile => $newFile) {
    replaceFile($oldFile, $newFile);
}

// Create .htaccess file to ensure proper URL rewriting
echo "\n=== Creating .htaccess file ===\n";
$htaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</IfModule>
EOT;

$htaccessFile = BASE_DIR . '/public/.htaccess';
if (!file_exists($htaccessFile)) {
    file_put_contents($htaccessFile, $htaccessContent);
    echo "Created .htaccess file\n";
} else {
    echo ".htaccess file already exists\n";
}

// Create a basic .env file if it doesn't exist
echo "\n=== Creating .env file if needed ===\n";
$envFile = BASE_DIR . '/.env';
if (!file_exists($envFile)) {
    $envContent = <<<EOT
APP_NAME="Gideon's Technology"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=UTC
APP_LOCALE=en

DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_NAME=gideons_tech
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
DB_PREFIX=
EOT;
    
    file_put_contents($envFile, $envContent);
    echo "Created .env file\n";
} else {
    echo ".env file already exists\n";
}

// Remove duplicate directories if they exist
echo "\n=== Cleaning up duplicate directories ===\n";
$duplicateDirs = [
    BASE_DIR . '/app/Core_temp',
    BASE_DIR . '/app/core_backup',
];

foreach ($duplicateDirs as $dir) {
    if (is_dir($dir)) {
        echo "Found duplicate directory: $dir\n";
        echo "Please review this directory manually and merge any needed files before deleting.\n";
    }
}

echo "\n=== Optimization Complete ===\n";
echo "The application structure has been optimized for better performance and maintainability.\n";
echo "Please review the changes and test the application to ensure everything is working correctly.\n";
