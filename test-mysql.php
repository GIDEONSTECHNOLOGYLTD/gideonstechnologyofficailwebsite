<?php
require_once __DIR__ . '/config/app.php';

try {
    // Try to connect to MySQL
    $dsn = "mysql:host=localhost;dbname=gtech_db";
    $username = "your_mysql_username"; // Replace with your cPanel MySQL username
    $password = "your_mysql_password"; // Replace with your cPanel MySQL password
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Successfully connected to MySQL!\n";
    
    // Test if we can create a table
    $pdo->exec("CREATE TABLE IF NOT EXISTS connection_test (
        id INT AUTO_INCREMENT PRIMARY KEY,
        test_column VARCHAR(255)
    )");
    
    echo "✓ Successfully created test table!\n";
    
    // Clean up
    $pdo->exec("DROP TABLE connection_test");
    
    echo "✓ Database connection and permissions verified!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nTo fix this:\n";
    echo "1. Go to your cPanel\n";
    echo "2. Find 'MySQL Databases'\n";
    echo "3. Create a new database named 'gtech_db'\n";
    echo "4. Create a new user or use existing one\n";
    echo "5. Add the user to the database with 'ALL PRIVILEGES'\n";
    exit(1);
}
