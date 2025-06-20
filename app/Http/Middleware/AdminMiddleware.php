<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * Process the admin middleware
     * 
     * @param Request $request The request
     * @param RequestHandler $handler The request handler
     * @return Response The response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Check if user is logged in first
        if (!isset($_SESSION['user_id'])) {
            // User is not logged in, redirect to login page
            $response = new SlimResponse();
            return $response->withHeader('Location', '/auth/login')
                           ->withStatus(302);
        }
        
        // Check if user is an admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // User is not an admin, redirect to dashboard with error
            $response = new SlimResponse();
            return $response->withHeader('Location', '/dashboard?error=access_denied')
                           ->withStatus(302);
        }
        
        // User is admin, proceed with request
        return $handler->handle($request);
    }
    
    /**
     * Called when middleware is used as callable
     *
     * @param Request $request The request
     * @param RequestHandler $handler The request handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        return $this->process($request, $handler);
    }
}