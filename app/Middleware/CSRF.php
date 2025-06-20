<?php
namespace App\Middleware;

use App\Core\Session;

class CSRF {
    private $tokenName = '_csrf_token';
    private $tokenExpire = 7200; // 2 hours
    private $headerName = 'X-CSRF-Token';

    public function handle($request, $next) {
        // Generate token if it doesn't exist
        if (!isset($_SESSION[$this->tokenName]) || 
            (isset($_SESSION[$this->tokenName . '_time']) && 
             $_SESSION[$this->tokenName . '_time'] < time() - $this->tokenExpire)) {
            $this->regenerateToken();
        }

        // Set token in response headers
        $response = $next($request);
        $response->headers->set('X-CSRF-Token', $this->getToken());

        return $response;
    }

    public function generateToken() {
        if (!isset($_SESSION[$this->tokenName])) {
            $token = bin2hex(random_bytes(32));
            $_SESSION[$this->tokenName] = $token;
            $_SESSION[$this->tokenName . '_time'] = time();
        }
        
        return $_SESSION[$this->tokenName];
    }

    public function getTokenInput() {
        $token = $this->generateToken();
        return "<input type='hidden' name='csrf_token' value='{$token}'>";
    }

    public function validateToken($token) {
        if (!isset($_SESSION[$this->tokenName])) {
            return false;
        }

        if ($_SESSION[$this->tokenName . '_time'] < time() - $this->tokenExpire) {
            return false;
        }

        return hash_equals($_SESSION[$this->tokenName], $token);
    }

    public function checkRequest() {
        // Get token from different sources
        $token = $this->getTokenFromRequest();
        
        if (!$token) {
            throw new \Exception('CSRF token missing');
        }

        if (!$this->validateToken($token)) {
            throw new \Exception('Invalid CSRF token');
        }
    }

    private function getTokenFromRequest() {
        // Check request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && 
            $_SERVER['REQUEST_METHOD'] !== 'PUT' && 
            $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return null;
        }

        // Try to get token from different sources
        $token = $_POST[$this->tokenName] ?? 
                 $_SERVER['HTTP_' . str_replace('-', '_', strtoupper($this->headerName))] ?? 
                 null;

        return $token;
    }

    public function regenerateToken() {
        unset($_SESSION[$this->tokenName]);
        unset($_SESSION[$this->tokenName . '_time']);
        return $this->generateToken();
    }

    public function getToken() {
        return $_SESSION[$this->tokenName] ?? null;
    }
}
