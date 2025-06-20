<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class FintechController
{
    protected $renderer;
    
    public function __construct()
    {
        $this->renderer = new PhpRenderer('../templates');
    }
    
    /**
     * Display fintech services page
     */
    public function index(Request $request, Response $response)
    {
        return $this->renderer->render($response, 'services/fintech.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y')
        ]);
    }
    
    /**
     * Process fintech service request
     */
    public function processRequest(Request $request, Response $response)
    {
        // Get form data
        $data = $request->getParsedBody();
        
        // In a real application, validate and process the request
        // For example: store in database, send email notification, etc.
        
        // For demo purposes, we'll just redirect to a thank you page
        return $this->renderer->render($response, 'services/fintech-request-success.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'requestData' => $data
        ]);
    }
}
