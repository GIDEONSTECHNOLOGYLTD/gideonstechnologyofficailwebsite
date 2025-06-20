<?php
namespace App\Core;

class Security {
    private static $instance = null;
    private $session;
    
    private function __construct() {
        $this->session = Session::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public function generateCSRFToken() {
        $token = bin2hex(random_bytes(32));
        $this->session->set('csrf_token', $token);
        return $token;
    }

    public function validateCSRFToken($token) {
        return hash_equals($this->session->get('csrf_token'), $token);
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }

    public function validateRequestMethod($method) {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            throw new \Exception('Invalid request method');
        }
    }

    public function validateRateLimit($key, $maxAttempts = 60, $decayMinutes = 1) {
        $attempts = $this->session->get("rate_limit_$key", 0);
        $lastAttempt = $this->session->get("rate_limit_last_$key", 0);
        
        if (time() - $lastAttempt > $decayMinutes * 60) {
            $attempts = 0;
        }
        
        if ($attempts >= $maxAttempts) {
            throw new \Exception('Too many attempts. Please try again later.');
        }
        
        $this->session->set("rate_limit_$key", $attempts + 1);
        $this->session->set("rate_limit_last_$key", time());
    }

    public function validateFileUpload($file, $allowedTypes, $maxSize = 5242880) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new \Exception('Invalid file parameters');
        }

        if ($file['size'] > $maxSize) {
            throw new \Exception('File size exceeds limit');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new \Exception('Invalid file type');
        }
    }

    public function generateSecureFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }

    public function validateJSON($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function encryptData($data, $key) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            base64_decode($key),
            0,
            $iv
        );
        return base64_encode($iv . $encrypted);
    }

    public function decryptData($data, $key) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            base64_decode($key),
            0,
            $iv
        );
    }

    private function __clone() {}
    public function __wakeup() {}
}
