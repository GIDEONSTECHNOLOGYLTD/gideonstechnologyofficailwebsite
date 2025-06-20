<?php
class SessionManager {
    private $conn;
    private $sessionLifetime = 1800; // 30 minutes

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->startSecureSession();
    }

    private function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.cookie_samesite', 'Lax');
            ini_set('session.gc_maxlifetime', $this->sessionLifetime);
            session_start();
        }
    }

    public function createUserSession($userId) {
        $token = bin2hex(random_bytes(32));
        $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        $userAgent = filter_var($_SERVER['HTTP_USER_AGENT'], FILTER_SANITIZE_STRING);
        $expiresAt = date('Y-m-d H:i:s', time() + $this->sessionLifetime);

        $stmt = $this->conn->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $userId, $token, $ip, $userAgent, $expiresAt);
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['session_token'] = $token;
            return true;
        }
        return false;
    }

    public function validateSession() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['session_token'])) {
            return false;
        }

        $stmt = $this->conn->prepare("SELECT * FROM user_sessions WHERE user_id = ? AND session_token = ? AND expires_at > NOW()");
        $stmt->bind_param('is', $_SESSION['user_id'], $_SESSION['session_token']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $this->destroySession();
            return false;
        }

        // Update session expiration
        $this->updateSessionExpiration($_SESSION['session_token']);
        return true;
    }

    private function updateSessionExpiration($token) {
        $newExpiry = date('Y-m-d H:i:s', time() + $this->sessionLifetime);
        $stmt = $this->conn->prepare("UPDATE user_sessions SET expires_at = ? WHERE session_token = ?");
        $stmt->bind_param('ss', $newExpiry, $token);
        $stmt->execute();
    }

    public function destroySession() {
        if (isset($_SESSION['session_token'])) {
            $stmt = $this->conn->prepare("DELETE FROM user_sessions WHERE session_token = ?");
            $stmt->bind_param('s', $_SESSION['session_token']);
            $stmt->execute();
        }

        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        session_destroy();
    }

    public function cleanExpiredSessions() {
        $this->conn->query("DELETE FROM user_sessions WHERE expires_at < NOW()");
    }
}
?>