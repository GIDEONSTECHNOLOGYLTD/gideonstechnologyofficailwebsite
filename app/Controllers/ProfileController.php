<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class ProfileController
{
    protected $renderer;
    
    public function __construct()
    {
        $this->renderer = new PhpRenderer('../templates');
    }
    
    public function index($request, $response, $args)
    {
        return $this->renderer->render($response, 'profile/index.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'joined' => '2023-01-15'
            ]
        ]);
    }
    
    public function edit($request, $response, $args)
    {
        return $this->renderer->render($response, 'profile/edit.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'joined' => '2023-01-15'
            ]
        ]);
    }
    
    public function update($request, $response, $args)
    {
        // In a real application, you would process the form data here
        // For now, we'll just redirect back to the profile page
        return $response->withHeader('Location', '/profile')->withStatus(302);
    }
}