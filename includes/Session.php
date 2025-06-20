<?php
class Session {
    private static $instance = null;
    private $started = false;

    private function __construct() {
        $this->start();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function start() {
        if ($this->started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => HTTPS_ENABLED,
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => true
            ]);
        }

        $this->started = true;
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        unset($_SESSION[$key]);
    }

    public function pull($key, $default = null) {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    public function flash($key, $value) {
        $this->set('_flash.' . $key, $value);
    }

    public function getFlash($key, $default = null) {
        return $this->pull('_flash.' . $key, $default);
    }

    public function hasFlash($key) {
        return $this->has('_flash.' . $key);
    }

    public function reflash() {
        $flash = array_filter(array_keys($_SESSION), function($key) {
            return strpos($key, '_flash.') === 0;
        });

        foreach ($flash as $key) {
            $this->set($key, $this->get($key));
        }
    }

    public function clearFlash() {
        $flash = array_filter(array_keys($_SESSION), function($key) {
            return strpos($key, '_flash.') === 0;
        });

        foreach ($flash as $key) {
            $this->remove($key);
        }
    }

    public function all() {
        return $_SESSION;
    }

    public function destroy() {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        $this->started = false;
    }

    public function regenerate($destroy = false) {
        if ($destroy) {
            $this->destroy();
        }
        session_regenerate_id(true);
    }

    public function token() {
        if (!$this->has('_token')) {
            $this->set('_token', bin2hex(random_bytes(32)));
        }
        return $this->get('_token');
    }

    public function validateToken($token) {
        return hash_equals($this->token(), $token);
    }

    public function isStarted() {
        return $this->started;
    }

    public function getId() {
        return session_id();
    }

    public function setId($id) {
        session_id($id);
    }

    public function getName() {
        return session_name();
    }

    public function setName($name) {
        session_name($name);
    }
}