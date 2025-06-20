<?php

namespace App\Core;

/**
 * CSRF Protection Class
 * 
 * Provides Cross-Site Request Forgery protection for forms
 */
class CsrfProtection
{
    /**
     * Generate a CSRF token and store it in the session
     *
     * @return string The generated token
     */
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Validate a submitted CSRF token against the one stored in session
     *
     * @param string $token The token to validate
     * @return bool Whether the token is valid
     */
    public static function validateToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Check if token matches and hasn't expired (30 minute lifetime)
        $isValid = hash_equals($_SESSION['csrf_token'], $token) && 
                  (time() - $_SESSION['csrf_token_time'] < 1800);
        
        // If valid, regenerate the token for the next request
        if ($isValid) {
            self::generateToken();
        }
        
        return $isValid;
    }
    
    /**
     * Generate a hidden input field containing the CSRF token
     *
     * @return string HTML for the hidden input field
     */
    public static function tokenField(): string
    {
        $token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
