<?php
namespace App\Providers;

use App\Core\Database;

class AuthServiceProvider {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function attempt($email, $password) {
        $user = $this->db->query(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        )->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            return true;
        }
        
        return false;
    }
    
    public function check() {
        return isset($_SESSION['user_id']);
    }
    
    public function user() {
        if (!$this->check()) {
            return null;
        }
        
        return $this->db->query(
            "SELECT * FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        )->fetch();
    }
    
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        session_destroy();
    }
}
