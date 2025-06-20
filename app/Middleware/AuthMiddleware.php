<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;

/**
 * Authentication Middleware
 * 
 * Checks if a user is logged in and redirects to login page if not
 */
class AuthMiddleware
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
        // Check if user is logged in
        if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
            // Store the requested URL for redirect after login
            $_SESSION['redirect_after_login'] = (string) $request->getUri();
            
            // Redirect to login page
            $response = new Response();
            return $response
                ->withHeader('Location', '/auth/login')
                ->withStatus(302);
        }

        // User is logged in, proceed to the route
        return $handler->handle($request);
    }
}
