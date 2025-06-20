<?php

/**
 * Session helper functions
 */

if (!function_exists('session')) {
    /**
     * Get a value from the session
     *
     * @param string $key The session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    function session($key = null, $default = null) 
    {
        if ($key === null) {
            return $_SESSION;
        }
        
        return $_SESSION[$key] ?? $default;
    }
}

/**
 * Check if a session key exists
 */
if (!function_exists('session_has')) {
    function session_has($key) 
    {
        return isset($_SESSION[$key]);
    }
}

/**
 * Global helper functions
 */

if (!function_exists('request')) {
    /**
     * Get the request instance
     *
     * @return \App\Core\Request
     */
    function request()
    {
        return new \App\Core\Request();
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL
     *
     * @param string $url URL to redirect to
     * @param array $with Flash data to pass with redirect
     * @param int $statusCode HTTP status code
     * @return void
     */
    function redirect($url, $with = [], $statusCode = 302)
    {
        // Set flash data if provided
        if (!empty($with)) {
            session_start();
            foreach ($with as $key => $value) {
                $_SESSION['flash_' . $key] = $value;
            }
        }

        header('Location: ' . $url, true, $statusCode);
        exit;
    }
}