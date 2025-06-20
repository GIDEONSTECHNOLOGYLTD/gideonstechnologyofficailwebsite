<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Migration.php';
require_once __DIR__ . '/Schema.php';

try {
    // Get all migration files
    $files = glob(__DIR__ . '/migrations/*.php');
    natsort($files); // Sort by filename

    // Run migrations
    foreach ($files as $file) {
        require_once $file;
        $baseName = pathinfo($file, PATHINFO_FILENAME);
        
        // Convert filename to class name
        $parts = explode('_', $baseName);
        array_shift($parts); // Remove date prefix
        $className = str_replace(' ', '', ucwords(implode(' ', $parts)));
        $fullClassName = 'Database\Migrations\\' . $className;
        
        // Try to instantiate the migration class
        if (class_exists($fullClassName)) {
            echo "Running migration: " . basename($file) . "\n";
            $migration = new $fullClassName();
            $migration->up();
            echo "✓ Migration completed\n";
        }
    }

} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
    exit(1);
}
