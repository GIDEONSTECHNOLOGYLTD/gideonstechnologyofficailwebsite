<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * Admin authentication middleware
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Check if user is logged in and is an admin
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            // Redirect to dashboard or access denied
            $response = new \Slim\Psr7\Response();
            return $response
                ->withHeader('Location', '/dashboard?error=access_denied')
                ->withStatus(302);
        }

        // User is admin, process the request
        return $handler->handle($request);
    }
}