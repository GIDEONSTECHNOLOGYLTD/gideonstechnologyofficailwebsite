<?php

namespace App\Http\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Base Controller
 * 
 * Provides common functionality for all controllers
 */
class Controller
{
    /**
     * Container
     *
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
     * Render a view
     *
     * @param Response $response
     * @param string $template
     * @param array $data
     * @return Response
     */
    protected function render(Response $response, string $template, array $data = []): Response
    {
        return $this->container->get('view')->render($response, $template, $data);
    }
    
    /**
     * Return JSON response
     *
     * @param Response $response
     * @param array $data
     * @param int $status
     * @return Response
     */
    protected function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
    
    /**
     * Return error response (404 Not Found)
     *
     * @param Response $response
     * @param string $message
     * @return Response
     */
    protected function notFound(Response $response, string $message = 'Not found'): Response
    {
        return $this->render($response, 'error/404.php', [
            'message' => $message
        ])->withStatus(404);
    }
    
    /**
     * Return error response (500 Server Error)
     *
     * @param Response $response
     * @param string $message
     * @return Response
     */
    protected function serverError(Response $response, string $message = 'Server error'): Response
    {
        return $this->render($response, 'error/500.php', [
            'message' => $message
        ])->withStatus(500);
    }
    
    /**
     * Redirect to route
     *
     * @param Response $response
     * @param string $route
     * @param array $args
     * @return Response
     */
    protected function redirectToRoute(Response $response, string $route, array $args = []): Response
    {
        return $response
            ->withHeader('Location', $this->container->get('router')->urlFor($route, $args))
            ->withStatus(302);
    }
    
    /**
     * Get base URL
     *
     * @return string
     */
    protected function getBaseUrl(): string
    {
        $request = $this->container->get('request');
        $uri = $request->getUri();
        
        return $uri->getScheme() . '://' . $uri->getHost() . 
               ($uri->getPort() ? ':' . $uri->getPort() : '');
    }
}