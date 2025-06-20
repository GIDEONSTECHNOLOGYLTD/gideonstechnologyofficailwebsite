<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Simple Test Controller
 * 
 * A standalone controller that doesn't require database connections
 */
class SimpleTestController
{
    /**
     * Simple test that doesn't use database connections or any dependencies
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response)
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
    
    /**
     * Simple HTML test
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function html(Request $request, Response $response)
    {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Simple Test Page</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; }
        .info { background: #f4f4f4; padding: 20px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Simple Test Page!</h1>
        <div class="info">
            <p>This is a test page that doesn't use Twig or database connections.</p>
            <p>Current server time: {$this->getCurrentTime()}</p>
        </div>
        <p>If you can see this page, the basic routing is working correctly!</p>
    </div>
</body>
</html>
HTML;
        
        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html');
    }
    
    /**
     * Get the current time
     *
     * @return string
     */
    private function getCurrentTime(): string
    {
        return date('Y-m-d H:i:s');
    }
}
