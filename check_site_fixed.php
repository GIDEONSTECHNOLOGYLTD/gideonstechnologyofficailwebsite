<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Site Check</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .ok { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .section { margin: 20px 0; padding: 10px; background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Gideons Technology - Site Check</h1>

    <div class="section">
        <h2>1. Database Check</h2>
        <?php
        try {
            $db = new PDO("mysql:host=localhost;dbname=gideonst_site", "gideonst_site", "Site2025@db");
            echo "<p class='ok'>✅ Database Connected!</p>";
            
            // Check if tables exist first
            $tables = [
                'users' => 'User accounts',
                'services' => 'Services list',
                'orders' => 'Customer orders',
                'payments' => 'Payment records',
                'settings' => 'Site settings'
            ];
            
            foreach($tables as $table => $description) {
                try {
                    // Check if table exists
                    $check = $db->query("SHOW TABLES LIKE '$table'");
                    if($check->rowCount() > 0) {
                        // Table exists, count records
                        $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                        echo "<p class='ok'>✅ $description ($count records)</p>";
                    } else {
                        echo "<p class='warning'>⚠️ $description: Table missing</p>";
                    }
                } catch(PDOException $e) {
                    echo "<p class='error'>❌ Error checking $description</p>";
                }
            }
        } catch(PDOException $e) {
            echo "<p class='error'>❌ Database Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>2. File Check</h2>
        <?php
        $files = [
            'index.php' => 'Main page',
            'includes/config.php' => 'Configuration file',
            'includes/database.php' => 'Database class',
            'includes/functions.php' => 'Helper functions',
            'assets/css/style.css' => 'Main stylesheet',
            'assets/js/main.js' => 'JavaScript file',
            '.htaccess' => 'Apache config'
        ];

        foreach($files as $file => $description) {
            if(file_exists($file)) {
                echo "<p class='ok'>✅ $description ($file)</p>";
            } else {
                echo "<p class='error'>❌ $description missing ($file)</p>";
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Database Actions</h2>
        <form method="post">
            <button type="submit" name="action" value="create_tables">Create Missing Tables</button>
            <button type="submit" name="action" value="add_admin">Create Admin User</button>
            <button type="submit" name="action" value="add_services">Add Sample Services</button>
        </form>
        
        <?php
        if(isset($_POST['action'])) {
            try {
                switch($_POST['action']) {
                    case 'create_tables':
                        // Read and execute SQL file
                        if(file_exists('database.sql')) {
                            $sql = file_get_contents('database.sql');
                            $db->exec($sql);
                            echo "<p class='ok'>✅ Tables created!</p>";
                        } else {
                            echo "<p class='error'>❌ database.sql not found</p>";
                        }
                        break;
                        
                    case 'add_admin':
                        $stmt = $db->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
                        $stmt->execute(['admin', 'admin@gideonstechnology.com', password_hash('admin123', PASSWORD_DEFAULT)]);
                        echo "<p class='ok'>✅ Admin user created!</p>";
                        break;
                        
                    case 'add_services':
                        $services = [
                            ['Website Development', 'Custom website development', 999.99, 'web'],
                            ['Tech Support', '24/7 technical support', 99.99, 'support'],
                            ['Database Design', 'Professional database design', 499.99, 'database']
                        ];
                        $stmt = $db->prepare("INSERT INTO services (name, description, price, category) VALUES (?, ?, ?, ?)");
                        foreach($services as $service) {
                            $stmt->execute($service);
                        }
                        echo "<p class='ok'>✅ Sample services added!</p>";
                        break;
                }
            } catch(PDOException $e) {
                echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Next Steps</h2>
        <ol>
            <li>If you see any ❌ errors above, use the buttons to fix them</li>
            <li>Make sure all files are in the correct locations</li>
            <li>Test the website: <a href="/" target="_blank">Open Website</a></li>
            <li>Try logging in with:
                <ul>
                    <li>Email: admin@gideonstechnology.com</li>
                    <li>Password: admin123</li>
                </ul>
            </li>
        </ol>
    </div>
</body>
</html>
