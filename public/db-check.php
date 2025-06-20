<?php

// Display all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Connection Test</h1>";

// Try to load .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    echo "<p>Found .env file</p>";
    
    // Simple .env file parser
    $envContent = file_get_contents(__DIR__ . '/../.env');
    $lines = explode("\n", $envContent);
    
    foreach ($lines as $line) {
        if (empty(trim($line)) || strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Remove quotes if present
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }
        
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
    
    echo "<p>Loaded environment variables from .env file</p>";
} else {
    echo "<p>No .env file found</p>";
}

// Get database connection details
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$database = getenv('DB_DATABASE') ?: 'gideons_tech';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

echo "<h2>Database Configuration</h2>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Port: $port</li>";
echo "<li>Database: $database</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . (empty($password) ? "<em>empty</em>" : "*****") . "</li>";
echo "</ul>";

// Test MySQL connection
echo "<h2>Connection Test</h2>";

try {
    // First try to connect without specifying a database
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Successfully connected to MySQL server</p>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
    $dbExists = $stmt->fetchColumn();
    
    if ($dbExists) {
        echo "<p style='color: green;'>✓ Database '$database' exists</p>";
        
        // Try to connect to the specific database
        $dsn = "mysql:host=$host;port=$port;dbname=$database";
        $dbPdo = new PDO($dsn, $username, $password);
        $dbPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color: green;'>✓ Successfully connected to the '$database' database</p>";
        
        // Get all tables in the database
        $stmt = $dbPdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>Database Tables</h3>";
        echo "<ul>";
        
        // Required tables for the application
        $requiredTables = [
            'users' => 'Stores user accounts and authentication data',
            'products' => 'Stores product information for the store',
            'categories' => 'Stores product categories',
            'orders' => 'Stores customer orders',
            'order_items' => 'Stores items within each order',
            'cart_items' => 'Stores items in user shopping carts',
            'password_resets' => 'Stores password reset tokens'
        ];
        
        $missingTables = [];
        
        foreach ($requiredTables as $tableName => $description) {
            if (in_array($tableName, $tables)) {
                // Table exists
                echo "<li style='color: green;'>✓ '$tableName' - $description</li>";
                
                // Count records
                $stmt = $dbPdo->query("SELECT COUNT(*) FROM $tableName");
                $count = $stmt->fetchColumn();
                echo "<ul><li>Contains $count records</li></ul>";
            } else {
                // Table doesn't exist
                echo "<li style='color: orange;'>⚠ '$tableName' - $description (missing)</li>";
                $missingTables[] = $tableName;
            }
        }
        
        echo "</ul>";
        
        // Show create table options for missing tables
        if (!empty($missingTables)) {
            echo "<h3>Missing Tables</h3>";
            echo "<p>The following tables are required but missing:</p>";
            echo "<ul>";
            
            foreach ($missingTables as $tableName) {
                echo "<li>'$tableName' - <a href='db-check.php?create_table=$tableName'>Create Table</a></li>";
            }
            
            echo "</ul>";
            echo "<p><a href='db-check.php?create_all_tables=1' class='btn btn-primary'>Create All Missing Tables</a></p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Database '$database' does not exist</p>";
        echo "<p>Would you like to create the database? <a href='db-check.php?create_db=1'>Create Database</a></p>";
    }
    
    // Handle database creation if requested
    if (isset($_GET['create_db']) && $_GET['create_db'] == 1) {
        $pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✓ Database '$database' created successfully!</p>";
        echo "<p><a href='db-check.php'>Refresh</a> to verify</p>";
    }
    
    // Handle table creation if requested
    if ((isset($_GET['create_table']) || isset($_GET['create_all_tables'])) && $dbExists) {
        $dsn = "mysql:host=$host;port=$port;dbname=$database";
        $dbPdo = new PDO($dsn, $username, $password);
        $dbPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Define table creation SQL statements
        $tableSql = [
            'users' => "CREATE TABLE `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `password` varchar(255) NOT NULL,
                `role` varchar(20) DEFAULT 'user',
                `status` varchar(20) DEFAULT 'active',
                `last_login` datetime DEFAULT NULL,
                `avatar` varchar(255) DEFAULT NULL,
                `bio` text DEFAULT NULL,
                `remember_token` varchar(100) DEFAULT NULL,
                `email_verified_at` datetime DEFAULT NULL,
                `username` varchar(50) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'products' => "CREATE TABLE `products` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `category_id` int(11) DEFAULT NULL,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `price` decimal(10,2) NOT NULL,
                `sale_price` decimal(10,2) DEFAULT NULL,
                `stock` int(11) DEFAULT 0,
                `sku` varchar(50) DEFAULT NULL,
                `featured` tinyint(1) DEFAULT 0,
                `status` varchar(20) DEFAULT 'active',
                `image` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `category_id` (`category_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'categories' => "CREATE TABLE `categories` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `parent_id` int(11) DEFAULT NULL,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `image` varchar(255) DEFAULT NULL,
                `status` varchar(20) DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `parent_id` (`parent_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'orders' => "CREATE TABLE `orders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) DEFAULT NULL,
                `order_number` varchar(50) NOT NULL,
                `status` varchar(20) DEFAULT 'pending',
                `total` decimal(10,2) NOT NULL,
                `tax` decimal(10,2) DEFAULT NULL,
                `shipping` decimal(10,2) DEFAULT NULL,
                `discount` decimal(10,2) DEFAULT NULL,
                `payment_method` varchar(50) DEFAULT NULL,
                `payment_status` varchar(20) DEFAULT 'pending',
                `shipping_address` text DEFAULT NULL,
                `billing_address` text DEFAULT NULL,
                `notes` text DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                UNIQUE KEY `order_number` (`order_number`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'order_items' => "CREATE TABLE `order_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `order_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `quantity` int(11) NOT NULL,
                `price` decimal(10,2) NOT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `order_id` (`order_id`),
                KEY `product_id` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'cart_items' => "CREATE TABLE `cart_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `quantity` int(11) NOT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `product_id` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'password_resets' => "CREATE TABLE `password_resets` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `email` varchar(255) NOT NULL,
                `token` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `email` (`email`),
                KEY `token` (`token`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        
        // Create a specific table if requested
        if (isset($_GET['create_table'])) {
            $tableName = $_GET['create_table'];
            
            if (isset($tableSql[$tableName])) {
                try {
                    $dbPdo->exec($tableSql[$tableName]);
                    echo "<p style='color: green;'>✓ '$tableName' table created successfully!</p>";
                } catch (PDOException $e) {
                    echo "<p style='color: red;'>✗ Error creating '$tableName' table: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Unknown table: '$tableName'</p>";
            }
        }
        
        // Create all missing tables if requested
        if (isset($_GET['create_all_tables']) && $_GET['create_all_tables'] == 1) {
            echo "<h3>Creating Missing Tables</h3>";
            echo "<ul>";
            
            // Get existing tables
            $stmt = $dbPdo->query("SHOW TABLES");
            $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tableSql as $tableName => $sql) {
                if (!in_array($tableName, $existingTables)) {
                    try {
                        $dbPdo->exec($sql);
                        echo "<li style='color: green;'>✓ '$tableName' table created successfully!</li>";
                    } catch (PDOException $e) {
                        echo "<li style='color: red;'>✗ Error creating '$tableName' table: " . $e->getMessage() . "</li>";
                    }
                }
            }
            
            echo "</ul>";
        }
        
        echo "<p><a href='db-check.php'>Refresh</a> to verify created tables</p>";
    }
    
    // For backward compatibility
    if (isset($_GET['create_users_table']) && $_GET['create_users_table'] == 1 && $dbExists) {
        header('Location: db-check.php?create_table=users');
        exit;
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Connection failed: " . $e->getMessage() . "</p>";
    
    // Provide suggestions based on the error
    if (strpos($e->getMessage(), "Access denied") !== false) {
        echo "<h3>Suggestions:</h3>";
        echo "<ul>";
        echo "<li>Check that your username and password are correct</li>";
        echo "<li>Make sure the MySQL user has appropriate permissions</li>";
        echo "<li>If using root with no password, try setting a password or using a different user</li>";
        echo "</ul>";
    } elseif (strpos($e->getMessage(), "Unknown database") !== false) {
        echo "<h3>Suggestions:</h3>";
        echo "<ul>";
        echo "<li>The database '$database' does not exist</li>";
        echo "<li>Create the database manually or use the link above</li>";
        echo "</ul>";
    } elseif (strpos($e->getMessage(), "Connection refused") !== false) {
        echo "<h3>Suggestions:</h3>";
        echo "<ul>";
        echo "<li>Check that MySQL is running on $host:$port</li>";
        echo "<li>Verify your firewall settings</li>";
        echo "</ul>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<p>Once the database connection is working:</p>";
echo "<ol>";
echo "<li>Update the database configuration in <code>/config/database.php</code> if needed</li>";
echo "<li>Make sure the application can connect to the database</li>";
echo "<li>Test the authentication routes: <a href='/auth/login' target='_blank'>/auth/login</a>, <a href='/auth/register' target='_blank'>/auth/register</a></li>";
echo "<li>Test other routes that require database access</li>";
echo "</ol>";
