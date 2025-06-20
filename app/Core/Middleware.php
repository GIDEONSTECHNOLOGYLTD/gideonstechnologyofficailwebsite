<?php
namespace App\Core;

abstract class Middleware {
    protected $next;
    
    public function setNext(Middleware $middleware) {
        $this->next = $middleware;
        return $middleware;
    }
    
    abstract public function handle($request);
    
    protected function runNext($request) {
        if ($this->next) {
            return $this->next->handle($request);
        }
        return true;
    }
}

class AuthMiddleware extends Middleware {
    public function handle($request) {
        if (!isset($_SESSION['user_id'])) {
            Response::unauthorized('Please login to continue');
            return false;
        }
        return $this->runNext($request);
    }
}

class CorsMiddleware extends Middleware {
    public function handle($request) {
        header('Access-Control-Allow-Origin: ' . Config::getInstance()->get('app.cors_origin', '*'));
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            Response::noContent();
            return false;
        }
        
        return $this->runNext($request);
    }
}

class RateLimitMiddleware extends Middleware {
    private $rateLimiter;
    
    public function __construct() {
        $this->rateLimiter = new RateLimiter();
    }
    
    public function handle($request) {
        $ip = $_SERVER['REMOTE_ADDR'];
        try {
            $this->rateLimiter->check("rate_limit:$ip");
            return $this->runNext($request);
        } catch (\Exception $e) {
            Response::error('Too many requests', 429);
            return false;
        }
    }
}

class ValidationMiddleware extends Middleware {
    private $rules;
    
    public function __construct(array $rules) {
        $this->rules = $rules;
    }
    
    public function handle($request) {
        $validator = new Validator();
        if (!$validator->make($_POST, $this->rules)) {
            Response::validationError($validator->getErrors());
            return false;
        }
        return $this->runNext($request);
    }
}