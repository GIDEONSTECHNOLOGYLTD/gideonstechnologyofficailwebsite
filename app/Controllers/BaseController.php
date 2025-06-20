<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;

/**
 * Base Controller
 * 
 * Provides common functionality for all controllers
 */
class BaseController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var Messages
     */
    protected $flash;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        
        // Initialize flash messages if available in container
        if ($container->has('flash')) {
            $this->flash = $container->get('flash');
        }
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
        if ($this->container->has('renderer')) {
            return $this->container->get('renderer')->render($response, $template, $data);
        }
        
        throw new \RuntimeException('Renderer not available in container');
    }
    
    /**
     * Get request input
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function input(string $key, $default = null)
    {
        $request = $this->container->get('request');
        $params = $request->getParsedBody();
        
        return $params[$key] ?? $default;
    }
    
    /**
     * Redirect to a path
     *
     * @param Response $response
     * @param string $path
     * @param int $status
     * @return Response
     */
    protected function redirect(Response $response, string $path, int $status = 302): Response
    {
        return $response->withHeader('Location', $path)->withStatus($status);
    }
}
