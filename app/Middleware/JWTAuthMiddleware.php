<?php

namespace App\Middleware;

use App\Core\JWT;
use App\Repositories\UserRepository;

/**
 * JWT Authentication Middleware
 * 
 * Verifies JWT tokens for API authentication
 */
class JWTAuthMiddleware
{
    /**
     * JWT utility instance
     * @var JWT
     */
    private $jwt;
    
    /**
     * User repository instance
     * @var UserRepository
     */
    private $userRepository;
    
    /**
     * Constructor
     * 
     * @param JWT $jwt JWT utility instance
     * @param UserRepository $userRepository User repository instance
     */
    public function __construct(JWT $jwt, UserRepository $userRepository)
    {
        $this->jwt = $jwt;
        $this->userRepository = $userRepository;
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
        // Get Authorization header
        $authHeader = $request->getHeaderLine('Authorization');
        
        // Extract token from header
        $token = JWT::getTokenFromHeader($authHeader);
        
        if (!$token) {
            return $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'success' => false,
                    'message' => 'Unauthorized: No token provided',
                    'error' => 'authentication_required'
                ]));
        }
        
        // Verify token
        $payload = $this->jwt->verify($token);
        
        if (!$payload) {
            return $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid token',
                    'error' => 'invalid_token'
                ]));
        }
        
        // Get user from payload
        if (!isset($payload['user_id'])) {
            return $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'success' => false,
                    'message' => 'Unauthorized: Invalid token payload',
                    'error' => 'invalid_token_payload'
                ]));
        }
        
        $user = $this->userRepository->find($payload['user_id']);
        
        if (!$user) {
            return $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'success' => false,
                    'message' => 'Unauthorized: User not found',
                    'error' => 'user_not_found'
                ]));
        }
        
        // Add user to request attributes
        $request = $request->withAttribute('user', $user);
        $request = $request->withAttribute('token_payload', $payload);
        
        // Call next middleware
        return $next($request, $response);
    }
}
