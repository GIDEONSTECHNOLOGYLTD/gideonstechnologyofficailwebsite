<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

/**
 * API Authentication Middleware
 * 
 * Verifies API tokens and authenticates API requests
 */
class ApiAuthMiddleware
{
    /**
     * Middleware invokable class
     *
     * @param Request $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Get API token from header
        $token = $request->getHeaderLine('Authorization');
        
        // Remove 'Bearer ' prefix if present
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }
        
        // If no token is provided
        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'unauthorized',
                    'message' => 'API token is required'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
        
        // Verify token (in a real app, you would validate against a database or JWT)
        // For demonstration, we'll use a simple check
        if (!$this->validateToken($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'invalid_token',
                    'message' => 'Invalid or expired API token'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }
        
        // Token is valid, proceed with the request
        return $handler->handle($request);
    }
    
    /**
     * Validate the API token
     *
     * @param string $token The API token to validate
     * @return bool Whether the token is valid
     */
    private function validateToken(string $token): bool
    {
        // In a real application, you would validate the token against your database
        // or verify a JWT signature
        
        // For demonstration purposes, we'll use a simple check
        // Replace this with your actual token validation logic
        $validTokens = [
            'test_api_token', // For testing
            // Add your actual tokens here or check against database
        ];
        
        return in_array($token, $validTokens);
    }
}
