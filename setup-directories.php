<?php

// Create required directories if they don't exist
$directories = [
    __DIR__ . '/database',
    __DIR__ . '/database/migrations',
    __DIR__ . '/database/seeds'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "Creating directory: {$dir}\n";
        mkdir($dir, 0755, true);
    } else {
        echo "Directory exists: {$dir}\n";
    }
}

echo "Directory setup complete.\n";