<?php
namespace App\Core;

class SessionManager {
    private const SESSION_LIFETIME = 3600;
    private const REGENERATE_TIME = 300;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            $this->configure();
            session_start();
        }
        $this->validate();
    }

    private function configure() {
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', self::SESSION_LIFETIME);
    }

    private function validate() {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['last_activity'] > self::SESSION_LIFETIME) {
            $this->destroy();
        }

        if (time() - $_SESSION['created'] > self::REGENERATE_TIME) {
            $this->regenerate();
        }

        $_SESSION['last_activity'] = time();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function remove($key) {
        unset($_SESSION[$key]);
    }

    public function regenerate() {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }

    public function destroy() {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }
}