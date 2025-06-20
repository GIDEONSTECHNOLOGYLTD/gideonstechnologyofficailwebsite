<?php
// Show all errors
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
            
            // Check tables
            $tables = [
                'users' => 'User accounts',
                'services' => 'Services list',
                'orders' => 'Customer orders',
                'payments' => 'Payment records',
                'settings' => 'Site settings'
            ];
            
            foreach($tables as $table => $description) {
                try {
                    $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                    echo "<p class='ok'>✅ $description ($count records)</p>";
                } catch(PDOException $e) {
                    echo "<p class='error'>❌ $description: Missing</p>";
                }
            }
        } catch(PDOException $e) {
            echo "<p class='error'>❌ Database Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>2. Admin Access</h2>
        <?php
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE is_admin = 1");
            $adminCount = $stmt->execute() ? $stmt->fetchColumn() : 0;
            if($adminCount > 0) {
                echo "<p class='ok'>✅ Admin account exists</p>";
            } else {
                echo "<p class='error'>❌ No admin account found</p>";
            }
        } catch(PDOException $e) {
            echo "<p class='error'>❌ Cannot check admin: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Important URLs</h2>
        <p>Main Site: <a href="https://gideonstechnology.com" target="_blank">gideonstechnology.com</a></p>
        <p>Adminer: <a href="https://gideonstechnology.com/adminer/adminer.php" target="_blank">Open Adminer</a></p>
    </div>

    <div class="section">
        <h2>4. Quick Actions</h2>
        <form method="post">
            <button type="submit" name="action" value="create_admin">Create Admin Account</button>
            <button type="submit" name="action" value="add_services">Add Sample Services</button>
        </form>
        
        <?php
        if(isset($_POST['action'])) {
            switch($_POST['action']) {
                case 'create_admin':
                    try {
                        $stmt = $db->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, 1)");
                        $stmt->execute(['admin', 'admin@gideonstechnology.com', password_hash('admin123', PASSWORD_DEFAULT)]);
                        echo "<p class='ok'>✅ Admin account created!</p>";
                    } catch(PDOException $e) {
                        echo "<p class='error'>❌ Error creating admin: " . $e->getMessage() . "</p>";
                    }
                    break;
                    
                case 'add_services':
                    try {
                        $services = [
                            ['Website Development', 999.99, 'web'],
                            ['Tech Support', 99.99, 'support'],
                            ['Database Design', 499.99, 'database']
                        ];
                        $stmt = $db->prepare("INSERT INTO services (name, price, category) VALUES (?, ?, ?)");
                        foreach($services as $service) {
                            $stmt->execute($service);
                        }
                        echo "<p class='ok'>✅ Sample services added!</p>";
                    } catch(PDOException $e) {
                        echo "<p class='error'>❌ Error adding services: " . $e->getMessage() . "</p>";
                    }
                    break;
            }
        }
        ?>
    </div>
</body>
</html>
