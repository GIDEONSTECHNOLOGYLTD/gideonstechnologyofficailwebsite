<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use App\Models\User;

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
     * Middleware logic
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        
        // Get session from container
        $session = $this->container->get('session');
        
        // Check if user is authenticated
        if (!$session->has('user_id')) {
            // Store the current URL for redirecting after login
            $session->set('redirect_after_login', (string)$request->getUri());
            
            // Add flash message
            $flash = $this->container->get('flash');
            $flash->addMessage('error', 'Please log in to access the admin area');
            
            // Redirect to login page
            $response = new \Slim\Psr7\Response();
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }
        
        // Get user from database
        $user = User::find($session->get('user_id'));
        
        // Check if user exists and has admin role
        if (!$user || !$this->isAdmin($user)) {
            // Add flash message
            $flash = $this->container->get('flash');
            $flash->addMessage('error', 'You do not have permission to access the admin area');
            
            // Redirect to home page
            $response = new \Slim\Psr7\Response();
            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }
        
        // Add user to request attributes for use in controllers
        $request = $request->withAttribute('user', $user);
        
        return $handler->handle($request);
    }
    
    /**
     * Check if user has admin role
     * 
     * @param User $user
     * @return bool
     */
    private function isAdmin(User $user): bool
    {
        // Check if user has admin or superadmin role
        return in_array($user->role, ['admin', 'superadmin']);
    }
}
