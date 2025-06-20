<?php

namespace Database\Seeds;

use PDO;

class UserSeeder {
    protected $pdo;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    public function run() {
        // Check if admin user already exists
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute(['admin@gideons.tech']);
        
        if ($stmt->fetchColumn() == 0) {
            // Create admin user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, password, full_name, role, status) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            // Default password is "password" - you should change this in production
            $hashedPassword = password_hash('password', PASSWORD_BCRYPT);
            
            $stmt->execute([
                'admin@gideons.tech',
                $hashedPassword,
                'Admin User',
                'admin',
                'active'
            ]);
            
            echo "Admin user created\n";
        } else {
            echo "Admin user already exists\n";
        }
        
        // Create sample users for testing
        $users = [
            [
                'email' => 'john@example.com',
                'password' => password_hash('user1pass', PASSWORD_BCRYPT),
                'full_name' => 'John Doe',
                'role' => 'user',
                'status' => 'active'
            ],
            [
                'email' => 'jane@example.com',
                'password' => password_hash('user2pass', PASSWORD_BCRYPT),
                'full_name' => 'Jane Smith',
                'role' => 'user',
                'status' => 'active'
            ]
        ];
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $insertStmt = $this->pdo->prepare("
            INSERT INTO users (email, password, full_name, role, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($users as $user) {
            $stmt->execute([$user['email']]);
            
            if ($stmt->fetchColumn() == 0) {
                $insertStmt->execute([
                    $user['email'],
                    $user['password'],
                    $user['full_name'],
                    $user['role'],
                    $user['status']
                ]);
                
                echo "Created user: {$user['email']}\n";
            } else {
                echo "User {$user['email']} already exists\n";
            }
        }
    }
}