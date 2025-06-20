<?php

namespace App\Core;

/**
 * Session Class
 * Manages session handling
 */
class Session
{
    /**
     * Initialize session
     * 
     * @return void
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set a session value
     * 
     * @param string $key Session key
     * @param mixed $value Session value
     * @return void
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session value
     * 
     * @param string $key Session key
     * @param mixed $default Default value if session key doesn't exist
     * @return mixed Session value or default
     */
    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session key exists
     * 
     * @param string $key Session key
     * @return bool True if session key exists
     */
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove a session key
     * 
     * @param string $key Session key
     * @return void
     */
    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Set flash message
     * 
     * @param string $key Flash key
     * @param mixed $value Flash value
     * @return void
     */
    public function setFlash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Get flash message
     * 
     * @param string $key Flash key
     * @param mixed $default Default value if flash key doesn't exist
     * @return mixed Flash value or default
     */
    public function getFlash($key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }
        
        return $value;
    }
    
    /**
     * Check if flash key exists
     * 
     * @param string $key Flash key
     * @return bool True if flash key exists
     */
    public function hasFlash($key)
    {
        return isset($_SESSION['_flash'][$key]);
    }
    
    /**
     * Clear all flash messages
     * 
     * @return void
     */
    public function clearFlash()
    {
        $_SESSION['_flash'] = [];
    }
    
    /**
     * Destroy session
     * 
     * @return void
     */
    public function destroy()
    {
        session_unset();
        session_destroy();
    }
}