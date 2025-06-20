<?php

namespace App\Middleware;

/**
 * Role-based Authorization Middleware
 * 
 * Restricts access to routes based on user roles
 */
class RoleMiddleware
{
    /**
     * Allowed roles for this middleware instance
     * @var array
     */
    private $allowedRoles;
    
    /**
     * Constructor
     * 
     * @param array|string $roles Allowed role(s)
     */
    public function __construct($roles)
    {
        $this->allowedRoles = is_array($roles) ? $roles : [$roles];
    }
    
    /**
     * Handle the middleware
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request PSR-7 request
     * @param \Psr\Http\Message\ResponseInterface $response PSR-7 response
     * @param callable $next Next middleware
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        // Get authenticated user from request attributes (set by JWTAuthMiddleware)
        $user = $request->getAttribute('user');
        
        // Check if user exists and has a role
        if (!$user || !isset($user->role)) {
            return $response->withStatus(403)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'success' => false,
                    'message' => 'Forbidden: Access denied',
                    'error' => 'access_denied'
                ]));
        }
        
        // Check if user role is allowed
        if (!in_array($user->role, $this->allowedRoles)) {
            return $response->withStatus(403)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'success' => false,
                    'message' => 'Forbidden: Insufficient permissions',
                    'error' => 'insufficient_permissions'
                ]));
        }
        
        // User has required role, proceed to next middleware
        return $next($request, $response);
    }
}
