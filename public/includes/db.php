<?php
// Include site config for DB constants
require_once __DIR__ . '/../../app/config/site.php';

// Include bootstrap if not already included
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../../app/bootstrap.php';
}

// Create database connection if not already created
if (!isset($pdo)) {
    try {
        $pdo = new PDO(
            DB_DSN,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Common database functions
function createUser($email, $password, $name) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password_hash, name, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $name
        ]);
    } catch(PDOException $e) {
        return false;
    }
}

function getUserByEmail($email) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return false;
    }
}

function updateUserPassword($userId, $newPassword) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET password_hash = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $userId
        ]);
    } catch(PDOException $e) {
        return false;
    }
}

function updateUserName($userId, $name) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare(
            "UPDATE users SET name = ?, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$name, $userId]);
    } catch(PDOException $e) {
        return false;
    }
}

function updateUserAvatar($userId, $avatarPath) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare(
            "UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?"
        );
        return $stmt->execute([$avatarPath, $userId]);
    } catch(PDOException $e) {
        return false;
    }
}

function createPasswordReset($email, $token) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (email, token, created_at) 
            VALUES (?, ?, NOW())
        ");
        
        return $stmt->execute([$email, $token]);
    } catch(PDOException $e) {
        return false;
    }
}

function getPasswordReset($token) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM password_resets 
            WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            AND used = 0
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        return false;
    }
}

function markPasswordResetUsed($token) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE password_resets 
            SET used = 1, updated_at = NOW() 
            WHERE token = ?
        ");
        
        return $stmt->execute([$token]);
    } catch(PDOException $e) {
        return false;
    }
}
