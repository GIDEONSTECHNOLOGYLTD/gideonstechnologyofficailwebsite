<?php

require_once __DIR__ . '/vendor/autoload.php';

if ($argc < 2) {
    echo "Usage: php create-migration.php migration_name\n";
    echo "Example: php create-migration.php create_users_table\n";
    exit(1);
}

// Get migration name
$migrationName = $argv[1];

// Format migration name for file and class
$parts = explode('_', $migrationName);
$className = '';
foreach ($parts as $part) {
    $className .= ucfirst($part);
}

// Create timestamp
$timestamp = date('YmdHis');

// Create file name
$fileName = "{$timestamp}_{$migrationName}.php";
$filePath = __DIR__ . '/database/migrations/' . $fileName;

// Get template
$templatePath = __DIR__ . '/database/migrations/migration_template.php';
if (!file_exists($templatePath)) {
    echo "Error: Template file not found at {$templatePath}\n";
    exit(1);
}

// Read template and replace class name
$template = file_get_contents($templatePath);
$template = str_replace('TemplateClassName', $className, $template);

// Write to file
if (file_put_contents($filePath, $template) !== false) {
    echo "Created migration: {$fileName}\n";
    echo "Migration class: {$className}\n";
} else {
    echo "Error: Failed to create migration file\n";
    exit(1);
}