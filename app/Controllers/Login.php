<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

/**
 * This controller should be merged with AuthController
 * @deprecated Use AuthController instead
 */
class Login extends BaseController
{
    public function __construct(PhpRenderer $renderer)
    {
        parent::__construct($renderer);
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, 'auth/login.php', [
            'title' => 'Login - Gideon\'s Technology',
            'page' => 'login'
        ]);
    }

    public function processLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        // Authentication logic would go here
        
        // For demo purposes, redirect to dashboard
        return $response->withHeader('Location', '/dashboard')
                       ->withStatus(302);
    }
}