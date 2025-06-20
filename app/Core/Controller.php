<?php
/**
 * Base Controller Class
 * 
 * This class serves as the base for all controllers in the application.
 * It provides common functionality and properties that all controllers need.
 */

namespace App\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Controller
{
    /**
     * @var ContainerInterface The DI container instance
     */
    protected $container;
    
    /**
     * @var array Application settings
     */
    protected $settings;
    
    /**
     * Constructor
     * 
     * @param ContainerInterface|null $container DI container instance
     */
    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->settings = $container ? $container->get('settings') : [];
    }
    
    /**
     * Get a service from the container
     * 
     * @param string $service Service name
     * @return mixed The service instance
     */
    protected function get(string $service)
    {
        return $this->container->get($service);
    }
    
    /**
     * Render a view with data
     * 
     * @param Response $response Response object
     * @param string $template Template name
     * @param array $data Data to pass to the template
     * @return Response Response with rendered template
     */
    protected function render(Response $response, string $template, array $data = []): Response
    {
        // Simple rendering for now - can be enhanced with a template engine
        $html = "<h1>{$template}</h1>";
        $html .= "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    
    /**
     * Return JSON response
     * 
     * @param Response $response Response object
     * @param array $data Data to return as JSON
     * @param int $status HTTP status code
     * @return Response Response with JSON data
     */
    protected function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
    
    /**
     * Redirect to a specific URL
     * 
     * @param Response $response Response object
     * @param string $url URL to redirect to
     * @param int $status HTTP status code
     * @return Response Response with redirect
     */
    protected function redirect(Response $response, string $url, int $status = 302): Response
    {
        return $response
            ->withHeader('Location', $url)
            ->withStatus($status);
    }
}
