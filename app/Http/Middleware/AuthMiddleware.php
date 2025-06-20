<?php
/**
 * DEPRECATED: This middleware has been moved to App\Middleware\AuthMiddleware
 * This file is kept for backward compatibility only.
 * Please use App\Middleware\AuthMiddleware in new code.
 */

namespace App\Http\Middleware;

use App\Core\Auth;
use App\Utilities\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response as SlimResponse;

/**
 * Authentication Middleware
 * 
 * Checks if user is authenticated and has proper permissions
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @var Auth Auth service
     */
    protected $auth;
    
    /**
     * @var Logger Logger instance
     */
    protected $logger;
    
    /**
     * @var array Routes that don't require authentication
     */
    protected $publicRoutes = [
        '/',
        '/login',
        '/register',
        '/forgot-password',
        '/reset-password',
        '/api/login',
        '/api/register',
    ];
    
    /**
     * Constructor
     * 
     * @param Auth|null $auth Auth service
     * @param Logger|null $logger Logger instance
     */
    public function __construct(?Auth $auth = null, ?Logger $logger = null)
    {
        $this->auth = $auth ?? new Auth();
        $this->logger = $logger ?? new Logger();
    }
    
    /**
     * Process middleware logic
     *
     * @param Request $request The request
     * @param RequestHandler $handler The handler
     * @return Response The response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Get route from request
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        
        // If no route found, skip middleware
        if (empty($route)) {
            return $handler->handle($request);
        }
        
        // Get route pattern
        $routePattern = $route->getPattern();
        
        // Check if route is public
        if (in_array($routePattern, $this->publicRoutes)) {
            return $handler->handle($request);
        }
        
        // Check if user is authenticated
        if (!Auth::check()) {
            // Check for API token if it's an API route
            if (strpos($routePattern, '/api/') === 0) {
                // Get bearer token from request
                $token = $this->getBearerToken($request);
                
                if ($token) {
                    // Validate token
                    $user = $this->auth->validateToken($token);
                    
                    if ($user) {
                        // User is authenticated, proceed
                        return $handler->handle($request);
                    }
                }
                
                // Invalid or missing token, return 401 Unauthorized
                $response = new SlimResponse();
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Unauthorized',
                    'data' => null
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(401);
            }
            
            // For web routes, redirect to login
            $response = new SlimResponse();
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }
        
        // If this is a role-restricted route, check if user has the required role
        $routeName = $route->getName();
        
        if (strpos($routeName, 'admin:') === 0 && !Auth::isAdmin()) {
            // User is not an admin, redirect to dashboard
            $response = new SlimResponse();
            return $response
                ->withHeader('Location', '/dashboard')
                ->withStatus(302);
        }
        
        // User is authenticated and has proper role, proceed
        return $handler->handle($request);
    }
    
    /**
     * Get bearer token from request
     *
     * @param Request $request
     * @return string|null
     */
    private function getBearerToken(Request $request): ?string
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            return null;
        }
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}