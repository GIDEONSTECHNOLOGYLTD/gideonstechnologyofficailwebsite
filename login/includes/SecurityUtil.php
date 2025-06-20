<?php
class SecurityUtil {
    private $conn;
    private $maxAttempts = 5;
    private $blockDuration = 900; // 15 minutes

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function checkRateLimit($ip) {
        $this->cleanupAttempts();
        
        $stmt = $this->conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result['attempts'] >= $this->maxAttempts;
    }

    public function logLoginAttempt($userId, $success = false) {
        $ip = $this->getClientIP();
        $stmt = $this->conn->prepare("INSERT INTO login_attempts (user_id, ip_address, success) VALUES (?, ?, ?)");
        $stmt->bind_param('isi', $userId, $ip, $success);
        return $stmt->execute();
    }

    private function cleanupAttempts() {
        // Use a transaction to ensure the cleanup operation is atomic
        $this->conn->begin_transaction();
        try {
            $this->conn->query("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Failed to clean up login attempts: " . $e->getMessage());
        }
    }

    public function validatePassword($password) {
        // Enforce strong password requirements:
        // - At least 8 characters
        // - Contains uppercase
        // - Contains lowercase
        // - Contains number
        // - Contains special character
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[0-9]/', $password) && 
               preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
    }

    public function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public function generateSecureToken() {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(32));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(32));
        } else {
            // Fallback method if more secure methods are unavailable
            $bytes = '';
            for ($i = 0; $i < 32; $i++) {
                $bytes .= chr(mt_rand(0, 255));
            }
            return bin2hex($bytes);
        }
    }

    public function isValidEmail($email) {
        // First use PHP's built-in filter
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        // Additional checks for common valid email patterns
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return preg_match($pattern, $email) === 1;
    }
    
    public function getClientIP() {
        // Get most reliable IP address
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // X-Forwarded-For may include multiple IPs, use the first one
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ipList[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // Validate IP address format
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            // Default to a placeholder if no valid IP is found
            $ip = '0.0.0.0';
        }
        
        return $ip;
    }
    
    // CSRF protection methods
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = $this->generateSecureToken();
        }
        return $_SESSION['csrf_token'];
    }
    
    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public function refreshCSRFToken() {
        $_SESSION['csrf_token'] = $this->generateSecureToken();
        return $_SESSION['csrf_token'];
    }
}
?>