<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Flash\Messages;

class TestController extends Controller
{
    /**
     * TestController constructor.
     *
     * @param Twig $view
     * @param Messages $flash
     */
    public function __construct(Twig $view, Messages $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }
    public function test(Request $request, Response $response)
    {
        try {
            // Render the test.twig template
            return $this->view->render($response, 'test.twig', [
                'name' => 'Test User',
                'time' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Log the error
            error_log('TestController error: ' . $e->getMessage());
            
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
    
    /**
     * Simple test that doesn't use database connections
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function simpleTest(Request $request, Response $response)
    {
        // Return a simple JSON response without using the database
        $data = [
            'status' => 'success',
            'message' => 'Simple test route is working',
            'time' => date('Y-m-d H:i:s')
        ];
        
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
