<?php
/**
 * SecurityAuditLogger - Tracks security-relevant events for audit purposes
 * 
 * This class provides centralized logging of security events to facilitate
 * security audits and incident response.
 */
class SecurityAuditLogger {
    private $conn;
    private $tableName = 'security_audit_log';
    private $logFile;
    private $logToDB = true;
    private $logToFile = true;
    
    /**
     * Constructor
     * 
     * @param mixed $dbConnection Database connection (PDO or mysqli)
     * @param string $logFilePath Path to log file (optional)
     */
    public function __construct($dbConnection = null, $logFilePath = null) {
        $this->conn = $dbConnection;
        
        if ($logFilePath === null) {
            $this->logFile = dirname(__DIR__) . '/logs/security_audit.log';
        } else {
            $this->logFile = $logFilePath;
        }
        
        // Create log directory if it doesn't exist
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Create audit table if it doesn't exist and we have a DB connection
        if ($this->conn !== null) {
            $this->ensureTableExists();
        } else {
            $this->logToDB = false;
        }
    }
    
    /**
     * Log a security event
     * 
     * @param string $eventType Type of security event
     * @param string $message Description of the event
     * @param array $context Additional contextual data
     * @param string $severity Event severity (info, warning, error, critical)
     * @param int $userId ID of the user related to the event (optional)
     * @return bool Success status
     */
    public function log($eventType, $message, $context = [], $severity = 'info', $userId = null) {
        $timestamp = date('Y-m-d H:i:s');
        $ipAddress = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $success = true;
        
        // Log to database if enabled
        if ($this->logToDB && $this->conn !== null) {
            $success = $success && $this->logToDatabase(
                $timestamp,
                $eventType,
                $message,
                json_encode($context),
                $severity,
                $userId,
                $ipAddress,
                $userAgent
            );
        }
        
        // Log to file if enabled
        if ($this->logToFile) {
            $success = $success && $this->logToFile(
                $timestamp,
                $eventType,
                $message,
                $context,
                $severity,
                $userId,
                $ipAddress,
                $userAgent
            );
        }
        
        // If this is a critical event, trigger an alert
        if ($severity === 'critical') {
            $this->triggerAlert($eventType, $message, $context);
        }
        
        return $success;
    }
    
    /**
     * Log an authentication event
     * 
     * @param bool $success Whether authentication was successful
     * @param string $username Username that was used
     * @param string $message Additional message
     * @param int $userId User ID (if successful login)
     * @return bool Success status
     */
    public function logAuthentication($success, $username, $message = '', $userId = null) {
        $eventType = $success ? 'authentication_success' : 'authentication_failure';
        $severity = $success ? 'info' : 'warning';
        
        // If failed login, check for repeated failures
        if (!$success) {
            $this->checkForBruteForceAttempts($username);
        }
        
        return $this->log(
            $eventType,
            $message ?: ($success ? 'Successful authentication' : 'Failed authentication attempt'),
            ['username' => $username],
            $severity,
            $userId
        );
    }
    
    /**
     * Log an access control event
     * 
     * @param string $resource Resource that was accessed or attempted
     * @param bool $allowed Whether access was allowed
     * @param string $message Additional message
     * @param int $userId User ID related to the event
     * @return bool Success status
     */
    public function logAccessControl($resource, $allowed, $message = '', $userId = null) {
        $eventType = $allowed ? 'access_granted' : 'access_denied';
        $severity = $allowed ? 'info' : 'warning';
        
        return $this->log(
            $eventType,
            $message ?: ($allowed ? "Access granted to $resource" : "Access denied to $resource"),
            ['resource' => $resource],
            $severity,
            $userId
        );
    }
    
    /**
     * Log a data modification event
     * 
     * @param string $dataType Type of data that was modified
     * @param string $action Action performed (create, update, delete)
     * @param mixed $identifier Identifier of the modified record
     * @param array $oldValues Previous values (for updates/deletes)
     * @param array $newValues New values (for creates/updates)
     * @param int $userId User who made the change
     * @return bool Success status
     */
    public function logDataModification($dataType, $action, $identifier, $oldValues = [], $newValues = [], $userId = null) {
        return $this->log(
            'data_' . $action,
            "Data $action for $dataType #$identifier",
            [
                'data_type' => $dataType,
                'identifier' => $identifier,
                'old_values' => $oldValues,
                'new_values' => $newValues
            ],
            'info',
            $userId
        );
    }
    
    /**
     * Log a security relevant configuration change
     * 
     * @param string $component Component that was changed
     * @param string $setting Setting that was changed
     * @param mixed $oldValue Previous value
     * @param mixed $newValue New value
     * @param int $userId User who made the change
     * @return bool Success status
     */
    public function logConfigChange($component, $setting, $oldValue, $newValue, $userId = null) {
        return $this->log(
            'config_change',
            "Configuration change in $component: $setting",
            [
                'component' => $component,
                'setting' => $setting,
                'old_value' => $oldValue,
                'new_value' => $newValue
            ],
            'warning', // Config changes are usually sensitive
            $userId
        );
    }
    
    /**
     * Log a potential security threat
     * 
     * @param string $threatType Type of threat detected
     * @param string $message Description of the threat
     * @param array $context Additional data about the threat
     * @param string $severity How severe the threat is
     * @return bool Success status
     */
    public function logThreat($threatType, $message, $context = [], $severity = 'warning') {
        return $this->log(
            'security_threat_' . $threatType,
            $message,
            $context,
            $severity
        );
    }
    
    /**
     * Search the audit log
     * 
     * @param array $criteria Search criteria
     * @param int $limit Maximum number of results
     * @param int $offset Result offset for pagination
     * @return array Search results
     */
    public function searchLogs($criteria = [], $limit = 100, $offset = 0) {
        if (!$this->logToDB || $this->conn === null) {
            return ['error' => 'Database logging not enabled'];
        }
        
        $query = "SELECT * FROM {$this->tableName} WHERE 1=1";
        $params = [];
        
        // Add criteria to query
        if (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $query .= " AND $field = ?";
                $params[] = $value;
            }
        }
        
        // Add date range if specified
        if (!empty($criteria['date_from'])) {
            $query .= " AND timestamp >= ?";
            $params[] = $criteria['date_from'];
        }
        
        if (!empty($criteria['date_to'])) {
            $query .= " AND timestamp <= ?";
            $params[] = $criteria['date_to'];
        }
        
        // Add order and limit
        $query .= " ORDER BY timestamp DESC LIMIT $offset, $limit";
        
        // Execute query based on connection type
        if ($this->conn instanceof PDO) {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Assume mysqli
            $stmt = $this->conn->prepare($query);
            if (count($params) > 0) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
            return $data;
        }
    }
    
    /**
     * Get audit statistics
     * 
     * @param string $period Time period to analyze (day, week, month)
     * @return array Statistics about security events
     */
    public function getStatistics($period = 'day') {
        if (!$this->logToDB || $this->conn === null) {
            return ['error' => 'Database logging not enabled'];
        }
        
        switch ($period) {
            case 'week':
                $timeframe = "timestamp >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $timeframe = "timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
            case 'day':
            default:
                $timeframe = "timestamp >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
                break;
        }
        
        // Get event counts by type
        $query = "SELECT event_type, COUNT(*) as count FROM {$this->tableName} 
                  WHERE $timeframe GROUP BY event_type ORDER BY count DESC";
        
        // Get severity distribution
        $severityQuery = "SELECT severity, COUNT(*) as count FROM {$this->tableName} 
                         WHERE $timeframe GROUP BY severity ORDER BY FIELD(severity, 'critical', 'error', 'warning', 'info')";
        
        // Execute queries based on connection type
        $eventCounts = [];
        $severityCounts = [];
        
        if ($this->conn instanceof PDO) {
            $stmt = $this->conn->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $eventCounts[$row['event_type']] = (int)$row['count'];
            }
            
            $stmt = $this->conn->query($severityQuery);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $severityCounts[$row['severity']] = (int)$row['count'];
            }
        } else {
            // Assume mysqli
            $result = $this->conn->query($query);
            while ($row = $result->fetch_assoc()) {
                $eventCounts[$row['event_type']] = (int)$row['count'];
            }
            
            $result = $this->conn->query($severityQuery);
            while ($row = $result->fetch_assoc()) {
                $severityCounts[$row['severity']] = (int)$row['count'];
            }
        }
        
        return [
            'period' => $period,
            'event_counts' => $eventCounts,
            'severity_counts' => $severityCounts,
            'total_events' => array_sum(array_values($eventCounts))
        ];
    }
    
    /**
     * Write event to database
     * 
     * @return bool Success status
     */
    private function logToDatabase($timestamp, $eventType, $message, $context, $severity, $userId, $ipAddress, $userAgent) {
        try {
            if ($this->conn instanceof PDO) {
                $stmt = $this->conn->prepare("INSERT INTO {$this->tableName} 
                                            (timestamp, event_type, message, context_data, severity, user_id, ip_address, user_agent) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                return $stmt->execute([
                    $timestamp, $eventType, $message, $context, $severity, $userId, $ipAddress, $userAgent
                ]);
            } else {
                // Assume mysqli
                $stmt = $this->conn->prepare("INSERT INTO {$this->tableName} 
                                            (timestamp, event_type, message, context_data, severity, user_id, ip_address, user_agent) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssss', $timestamp, $eventType, $message, $context, $severity, $userId, $ipAddress, $userAgent);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } catch (\Exception $e) {
            // If DB logging fails, log to file
            $errorMsg = "Error logging to database: " . $e->getMessage();
            file_put_contents(
                $this->logFile,
                "[" . date('Y-m-d H:i:s') . "] ERROR: {$errorMsg}\n",
                FILE_APPEND
            );
            return false;
        }
    }
    
    /**
     * Write event to log file
     * 
     * @return bool Success status
     */
    private function logToFile($timestamp, $eventType, $message, $context, $severity, $userId, $ipAddress, $userAgent) {
        $logEntry = sprintf(
            "[%s] [%s] [%s] [User:%s] [IP:%s] %s %s\n",
            $timestamp,
            strtoupper($severity),
            $eventType,
            $userId ?? 'guest',
            $ipAddress,
            $message,
            json_encode($context)
        );
        
        return file_put_contents($this->logFile, $logEntry, FILE_APPEND) !== false;
    }
    
    /**
     * Trigger an alert for critical events
     */
    private function triggerAlert($eventType, $message, $context) {
        // In a real system, this would send alerts via email, SMS, Slack, etc.
        $alertMessage = sprintf(
            "SECURITY ALERT - %s: %s\nContext: %s\nTime: %s\nIP: %s",
            $eventType,
            $message,
            json_encode($context),
            date('Y-m-d H:i:s'),
            $this->getClientIP()
        );
        
        // For now, just log the alert
        error_log($alertMessage);
        
        // Send email to admin if mail function is available
        if (function_exists('mail')) {
            $adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@example.com';
            mail(
                $adminEmail,
                "SECURITY ALERT - " . $eventType,
                $alertMessage,
                "From: security@" . $_SERVER['SERVER_NAME']
            );
        }
    }
    
    /**
     * Check for potential brute force attacks
     */
    private function checkForBruteForceAttempts($username) {
        if (!$this->logToDB || $this->conn === null) {
            return;
        }
        
        // Count failed login attempts for this username in the last 10 minutes
        $timeWindow = 10; // minutes
        
        if ($this->conn instanceof PDO) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->tableName} 
                                        WHERE event_type = 'authentication_failure' 
                                        AND context_data LIKE ? 
                                        AND timestamp >= DATE_SUB(NOW(), INTERVAL ? MINUTE)");
            $searchPattern = '%"username":"' . $username . '"%';
            $stmt->execute([$searchPattern, $timeWindow]);
            $count = $stmt->fetchColumn();
        } else {
            // Assume mysqli
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->tableName} 
                                        WHERE event_type = 'authentication_failure' 
                                        AND context_data LIKE ? 
                                        AND timestamp >= DATE_SUB(NOW(), INTERVAL ? MINUTE)");
            $searchPattern = '%"username":"' . $username . '"%';
            $stmt->bind_param('si', $searchPattern, $timeWindow);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $count = $result['count'];
            $stmt->close();
        }
        
        // Log a potential brute force attack if threshold is exceeded
        if ($count >= 5) {
            $this->log(
                'potential_brute_force',
                "Potential brute force attack detected",
                ['username' => $username, 'attempts' => $count, 'time_window' => $timeWindow . 'm'],
                'error'
            );
        }
    }
    
    /**
     * Create the audit log table if it doesn't exist
     */
    private function ensureTableExists() {
        $schema = "
        CREATE TABLE IF NOT EXISTS {$this->tableName} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            timestamp DATETIME NOT NULL,
            event_type VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            context_data TEXT,
            severity ENUM('info', 'warning', 'error', 'critical') NOT NULL DEFAULT 'info',
            user_id VARCHAR(36),
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            INDEX idx_timestamp (timestamp),
            INDEX idx_event_type (event_type),
            INDEX idx_severity (severity),
            INDEX idx_user_id (user_id)
        )";
        
        try {
            if ($this->conn instanceof PDO) {
                $this->conn->exec($schema);
            } else {
                // Assume mysqli
                $this->conn->query($schema);
            }
            return true;
        } catch (\Exception $e) {
            error_log("Error creating audit log table: " . $e->getMessage());
            $this->logToDB = false;
            return false;
        }
    }
    
    /**
     * Get client IP address securely
     * 
     * @return string Client IP address
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
}