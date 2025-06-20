<?php
echo "Starting post-deployment tasks...\n";

// 1. Connect to MySQL and create database if it doesn't exist
try {
    $pdo = new PDO(
        "mysql:host=localhost",
        "gideonst_db",  // Your cPanel MySQL username
        "08132005010Aa"   // Your cPanel MySQL password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS gideonst_db");
    echo "✓ Database created/verified\n";
    
    // Switch to the database
    $pdo->exec("USE gideonst_db");
    
    // Run migrations
    echo "Running migrations...\n";
    require_once __DIR__ . '/database/migrate.php';
    
    // Seed production data
    echo "Seeding production data...\n";
    require_once __DIR__ . '/database/seed_production.php';
    
    echo "✓ Post-deployment tasks completed!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nTo fix this:\n";
    echo "1. Go to cPanel > MySQL Databases\n";
    echo "2. Create database 'gtech_db'\n";
    echo "3. Create MySQL user if you haven't\n";
    echo "4. Add user to database with 'ALL PRIVILEGES'\n";
    exit(1);
}
