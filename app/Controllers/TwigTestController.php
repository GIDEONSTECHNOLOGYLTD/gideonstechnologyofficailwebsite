<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

/**
 * Twig Test Controller
 * 
 * A standalone controller for testing Twig templates without database access
 */
class TwigTestController
{
    /**
     * @var Twig
     */
    protected $view;
    
    /**
     * TwigTestController constructor
     * 
     * @param Twig $view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }
    
    /**
     * Render a Twig template
     * 
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // Render the test.twig template
            return $this->view->render($response, 'test.twig', [
                'name' => 'Twig Test User',
                'time' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Log the error
            error_log('TwigTestController error: ' . $e->getMessage());
            
            // Return a 500 error response
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ]));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}
