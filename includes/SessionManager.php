<?php
/**
 * SessionManager - Handles secure session management
 * 
 * This class provides methods for secure session handling, including prevention of
 * session hijacking, session fixation, and implementing proper logout procedures.
 */
class SessionManager {
    private $sessionName = 'GTECH_SESSID';
    private $sessionLifetime = 7200; // 2 hours in seconds
    private $regenerateIdInterval = 900; // 15 minutes in seconds
    private $secureOnly = true;
    private $httpOnly = true;
    private $sameSite = 'Strict';
    private $path = '/';
    
    /**
     * Constructor - initializes secure session settings
     */
    public function __construct() {
        // Ensure HTTPS in production
        if (!$this->isLocalhost() && empty($_SERVER['HTTPS'])) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit();
        }
        
        $this->setupSecureSessionSettings();
    }
    
    /**
     * Determine if the current request is from localhost
     * 
     * @return bool True if request is from localhost
     */
    private function isLocalhost() {
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip === '127.0.0.1' || $ip === '::1' || substr($ip, 0, 7) === '192.168';
    }
    
    /**
     * Configure secure session settings
     */
    private function setupSecureSessionSettings() {
        // Set secure session parameters
        $params = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $this->sessionLifetime,
            'path' => $this->path,
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => $this->secureOnly && !$this->isLocalhost(),
            'httponly' => $this->httpOnly,
            'samesite' => $this->sameSite
        ]);

        // Use our custom session name
        session_name($this->sessionName);
    }
    
    /**
     * Start a new session or resume an existing one
     * 
     * @return bool True if session was started successfully
     */
    public function startSession() {
        // Check for existing session
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }
        
        // Start session
        if (session_start()) {
            // Check if we need to regenerate the session ID
            if ($this->shouldRegenerateId()) {
                $this->regenerateSessionId();
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if session ID should be regenerated
     * 
     * @return bool True if session ID should be regenerated
     */
    private function shouldRegenerateId() {
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
            return false;
        }
        
        return (time() - $_SESSION['last_regeneration']) > $this->regenerateIdInterval;
    }
    
    /**
     * Regenerate session ID to prevent session fixation attacks
     * 
     * @return void
     */
    public function regenerateSessionId() {
        // Regenerate the session ID and keep existing session data
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
        
        // Update user's last activity time
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Set session data
     * 
     * @param string $key The session key
     * @param mixed $value The session value
     * @return void
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session data
     * 
     * @param string $key The session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The session value or default
     */
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Remove a specific session value
     * 
     * @param string $key The session key to remove
     * @return void
     */
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Check if a session variable exists
     * 
     * @param string $key The session key to check
     * @return bool True if the key exists
     */
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Destroy the session completely
     * 
     * @return bool True if session was destroyed successfully
     */
    public function destroy() {
        // Clear all session data
        $_SESSION = [];
        
        // Get session parameters
        $params = session_get_cookie_params();
        
        // Delete the session cookie
        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite']
            ]
        );
        
        // Destroy the session
        return session_destroy();
    }
    
    /**
     * Check if the session is expired
     * 
     * @return bool True if session is expired
     */
    public function isExpired() {
        // If no activity time is set, then it's a new session
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return false;
        }
        
        // Check if session has timed out
        if (time() - $_SESSION['last_activity'] > $this->sessionLifetime) {
            return true;
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
        return false;
    }
    
    /**
     * Authenticate a user
     * 
     * @param array $userData User information to store in session
     * @param bool $rememberMe Whether to create a persistent login
     * @return void
     */
    public function authenticate($userData, $rememberMe = false) {
        // Regenerate session ID when authenticating to prevent session fixation
        $this->regenerateSessionId();
        
        // Store user data in session
        $_SESSION['user_id'] = $userData['id'] ?? null;
        $_SESSION['user_email'] = $userData['email'] ?? null;
        $_SESSION['user_role'] = $userData['role'] ?? 'user';
        $_SESSION['authenticated'] = true;
        $_SESSION['auth_time'] = time();
        
        // Store additional security information
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Create a persistent login cookie if requested
        if ($rememberMe) {
            $this->createRememberMeToken($userData['id']);
        }
    }
    
    /**
     * Create a persistent login token ("remember me")
     * 
     * @param int $userId User ID to create token for
     * @return bool True on success
     */
    private function createRememberMeToken($userId) {
        // Generate a secure random token
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        
        // Store the hash of the validator in your database along with the selector
        $hash = hash('sha256', $validator);
        $expires = time() + (30 * 86400); // 30 days
        
        // Store in database
        $this->saveRememberMeToken($userId, $selector, $hash, $expires);
        
        // Create the cookie with the selector:validator pair
        $cookie = $selector . ':' . $validator;
        
        return setcookie(
            'remember_me',
            $cookie,
            [
                'expires' => $expires,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => !$this->isLocalhost(),
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }

    /**
     * Save a remember me token to the database
     *
     * @param int $userId User ID
     * @param string $selector Token selector
     * @param string $hash Hashed validator
     * @param int $expires Expiration timestamp
     * @return bool Success status
     */
    private function saveRememberMeToken($userId, $selector, $hash, $expires) {
        // Implementation depends on your database access method
        try {
            // Example implementation using a database connection
            // This assumes you have a database connection available in your project
            global $conn; // Or use a connection from dependency injection
            
            if (isset($conn)) {
                if ($conn instanceof PDO) {
                    $stmt = $conn->prepare("INSERT INTO auth_tokens 
                                           (user_id, selector, validator, expires) 
                                           VALUES (?, ?, ?, FROM_UNIXTIME(?))
                                           ON DUPLICATE KEY UPDATE 
                                           validator = VALUES(validator), 
                                           expires = VALUES(expires)");
                    return $stmt->execute([$userId, $selector, $hash, $expires]);
                } else {
                    // Assume MySQLi
                    $stmt = $conn->prepare("INSERT INTO auth_tokens 
                                           (user_id, selector, validator, expires) 
                                           VALUES (?, ?, ?, FROM_UNIXTIME(?))
                                           ON DUPLICATE KEY UPDATE 
                                           validator = VALUES(validator), 
                                           expires = VALUES(expires)");
                    $stmt->bind_param('issi', $userId, $selector, $hash, $expires);
                    $result = $stmt->execute();
                    $stmt->close();
                    return $result;
                }
            }
            
            // If no connection available, log the token for debugging
            error_log("Remember me token created: [User: $userId, Selector: $selector, Expires: " . date('Y-m-d H:i:s', $expires) . "]");
            return true;
        } catch (Exception $e) {
            error_log("Error saving remember me token: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate session security (prevent session hijacking)
     * 
     * @return bool True if session appears secure
     */
    public function validateSession() {
        if (!$this->isAuthenticated()) {
            return true; // No authentication to validate
        }
        
        // Check if IP address has changed
        if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            return false;
        }
        
        // Check if user agent has changed
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $userAgent) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool True if user is authenticated
     */
    public function isAuthenticated() {
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }
    
    /**
     * Check if user has required role
     * 
     * @param string|array $requiredRole Role(s) to check
     * @return bool True if user has role
     */
    public function hasRole($requiredRole) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        $userRole = $_SESSION['user_role'] ?? 'guest';
        
        if (is_array($requiredRole)) {
            return in_array($userRole, $requiredRole);
        }
        
        return $userRole === $requiredRole;
    }
}