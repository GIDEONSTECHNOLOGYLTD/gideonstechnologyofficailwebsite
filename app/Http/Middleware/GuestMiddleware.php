<?php

namespace App\Http\Middleware;

use App\Core\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * Guest Middleware
 * 
 * Ensures that only non-authenticated users can access certain routes
 * Redirects authenticated users to the dashboard
 */
class GuestMiddleware implements MiddlewareInterface
{
    /**
     * Process the middleware
     *
     * @param Request $request The request
     * @param RequestHandler $handler The handler
     * @return Response The response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            $responseFactory = new ResponseFactory();
            $response = $responseFactory->createResponse(302);
            return $response->withHeader('Location', '/dashboard');
        }
        
        return $handler->handle($request);
    }
}