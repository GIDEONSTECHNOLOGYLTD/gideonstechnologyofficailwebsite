<?php
/**
 * RateLimiter - Provides rate limiting functionality for API endpoints and sensitive operations
 * 
 * This class helps protect against brute force attacks and DoS attempts by limiting
 * how many requests a user can make within a specific timeframe.
 */
class RateLimiter {
    private $conn;
    private $tableName = 'rate_limits';
    private $ipAddress;
    
    /**
     * Constructor
     * 
     * @param PDO|mysqli $dbConnection Database connection
     * @param string $ip IP address (optional, uses current client IP if not provided)
     */
    public function __construct($dbConnection, $ip = null) {
        $this->conn = $dbConnection;
        $this->ipAddress = $ip ?: $this->getClientIP();
        $this->ensureTableExists();
    }
    
    /**
     * Check if the client has exceeded the rate limit for a specific action
     * 
     * @param string $action The action to check (e.g., 'login', 'api_request')
     * @param int $maxAttempts Maximum allowed attempts in the time window
     * @param int $timeWindow Time window in seconds
     * @return bool True if rate limit is exceeded, false otherwise
     */
    public function isLimited($action, $maxAttempts = 5, $timeWindow = 300) {
        // Clean up old rate limit records
        $this->cleanupOldRecords();
        
        // Count attempts for this IP and action within the time window
        $query = "SELECT COUNT(*) FROM {$this->tableName} 
                 WHERE ip_address = ? AND action = ? AND timestamp > NOW() - INTERVAL ? SECOND";
        
        if ($this->conn instanceof PDO) {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->ipAddress, $action, $timeWindow]);
            $count = $stmt->fetchColumn();
        } else {
            // Assuming MySQLi
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('ssi', $this->ipAddress, $action, $timeWindow);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
        }
        
        if ($count >= $maxAttempts) {
            // Log excessive attempts
            $this->logRateLimitExceeded($action, $maxAttempts);
            return true;
        }
        return false;
    }
    
    /**
     * Record an attempt for rate limiting
     * 
     * @param string $action The action being performed
     * @param string $userId Optional user ID
     * @return void
     */
    public function logAttempt($action, $userId = null) {
        if ($this->conn instanceof PDO) {
            $stmt = $this->conn->prepare("INSERT INTO {$this->tableName} 
                                        (ip_address, action, user_id, timestamp) 
                                        VALUES (?, ?, ?, NOW())");
            $stmt->execute([$this->ipAddress, $action, $userId]);
        } else {
            // Assuming MySQLi
            $stmt = $this->conn->prepare("INSERT INTO {$this->tableName} 
                                        (ip_address, action, user_id, timestamp) 
                                        VALUES (?, ?, ?, NOW())");
            $stmt->bind_param('sss', $this->ipAddress, $action, $userId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * Calculate time remaining until rate limit resets
     * 
     * @param string $action The action to check
     * @param int $timeWindow Time window in seconds
     * @return int Seconds until the rate limit resets
     */
    public function getTimeRemaining($action, $timeWindow = 300) {
        if ($this->conn instanceof PDO) {
            $stmt = $this->conn->prepare("SELECT TIMESTAMPDIFF(SECOND, NOW(), 
                                        MIN(timestamp) + INTERVAL ? SECOND) as time_remaining 
                                        FROM {$this->tableName} 
                                        WHERE ip_address = ? AND action = ? 
                                        AND timestamp > NOW() - INTERVAL ? SECOND");
            $stmt->execute([$timeWindow, $this->ipAddress, $action, $timeWindow]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Assuming MySQLi
            $stmt = $this->conn->prepare("SELECT TIMESTAMPDIFF(SECOND, NOW(), 
                                        MIN(timestamp) + INTERVAL ? SECOND) as time_remaining 
                                        FROM {$this->tableName} 
                                        WHERE ip_address = ? AND action = ? 
                                        AND timestamp > NOW() - INTERVAL ? SECOND");
            $stmt->bind_param('issi', $timeWindow, $this->ipAddress, $action, $timeWindow);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
        
        return max(0, $result['time_remaining'] ?: 0);
    }
    
    /**
     * Clean up old rate limit records to keep the table size manageable
     */
    private function cleanupOldRecords() {
        // Keep records for 24 hours for auditing, delete older ones
        if ($this->conn instanceof PDO) {
            $this->conn->exec("DELETE FROM {$this->tableName} WHERE timestamp < NOW() - INTERVAL 24 HOUR");
        } else {
            $this->conn->query("DELETE FROM {$this->tableName} WHERE timestamp < NOW() - INTERVAL 24 HOUR");
        }
    }
    
    /**
     * Make sure the rate limits table exists
     */
    private function ensureTableExists() {
        $schema = "
        CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            action VARCHAR(50) NOT NULL,
            user_id VARCHAR(50),
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip_action (ip_address, action),
            INDEX idx_timestamp (timestamp)
        )";
        
        if ($this->conn instanceof PDO) {
            $this->conn->exec($schema);
        } else {
            $this->conn->query($schema);
        }
    }
    
    /**
     * Get client IP address securely
     * 
     * @return string The client IP address
     */
    private function getClientIP() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ipList[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // Validate IP address format
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = '0.0.0.0';
        }
        
        return $ip;
    }
    
    /**
     * Log when a rate limit is exceeded (for security auditing)
     * 
     * @param string $action The action that was rate limited
     * @param int $limit The rate limit that was exceeded
     */
    private function logRateLimitExceeded($action, $limit) {
        $message = "Rate limit exceeded for action '{$action}' (limit: {$limit}) from IP: {$this->ipAddress}";
        error_log($message);
        
        // Optional: Log to a security events table
        if ($this->conn instanceof PDO) {
            $stmt = $this->conn->prepare("INSERT INTO security_events 
                                        (event_type, ip_address, details) 
                                        VALUES ('rate_limit_exceeded', ?, ?)");
            $stmt->execute([$this->ipAddress, $message]);
        } else if (method_exists($this->conn, 'prepare')) {
            $stmt = $this->conn->prepare("INSERT INTO security_events 
                                        (event_type, ip_address, details) 
                                        VALUES ('rate_limit_exceeded', ?, ?)");
            $stmt->bind_param('ss', $this->ipAddress, $message);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>