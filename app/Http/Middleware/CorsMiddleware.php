<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * CORS Middleware
 * 
 * Handles Cross-Origin Resource Sharing (CORS) headers for the application
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * Process the middleware
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Define allowed origins (you can make this more restrictive in production)
        $allowedOrigins = [
            'http://localhost:8080',
            'http://localhost:3000',
            'http://localhost',
            'https://gideonstechnology.com',
            // Add more as needed
        ];

        $origin = $request->getHeaderLine('Origin');
        
        // Create response
        $response = $handler->handle($request);
        
        // If the origin is in our allowed list or we're allowing all origins
        if (in_array($origin, $allowedOrigins) || empty($allowedOrigins)) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin);
        } elseif (empty($origin) && !empty($allowedOrigins)) {
            // If no origin is specified but we have allowed origins, use the first one
            $response = $response->withHeader('Access-Control-Allow-Origin', $allowedOrigins[0]);
        } else {
            // Fallback for development
            $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        }
        
        // Handle preflight OPTIONS requests
        if ($request->getMethod() === 'OPTIONS') {
            $response = $response
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN')
                ->withHeader('Access-Control-Max-Age', '86400'); // 24 hours cache
        }
        
        // Additional CORS headers for all responses
        return $response
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Expose-Headers', 'Content-Length, Content-Range');
    }
}