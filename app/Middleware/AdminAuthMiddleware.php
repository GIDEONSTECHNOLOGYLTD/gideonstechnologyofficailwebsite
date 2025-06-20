<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;

/**
 * Admin Authorization Middleware
 * 
 * Checks if a logged-in user has admin role
 */
class AdminAuthMiddleware
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
        // Check if user is logged in and has admin role
        if (!isset($_SESSION['user']) || 
            !isset($_SESSION['user']['role']) || 
            $_SESSION['user']['role'] !== 'admin') {
            
            // User is not an admin, redirect to login page
            $response = new Response();
            
            // Store the requested URL for redirect after login
            $_SESSION['redirect_after_login'] = $request->getUri()->getPath();
            
            // Add a flash message if flash is available
            if ($this->container->has('flash')) {
                $this->container->get('flash')->addMessage('error', 'Access denied. You must be an administrator to view this page.');
            }
            
            // If user is logged in but not admin, redirect to access-denied
            if (isset($_SESSION['user'])) {
                return $response
                    ->withHeader('Location', '/auth/access-denied')
                    ->withStatus(302);
            }
            
            // If user is not logged in, redirect to login page
            return $response
                ->withHeader('Location', '/auth/login')
                ->withStatus(302);
        }

        // User is admin, proceed to the route
        return $handler->handle($request);
    }
}