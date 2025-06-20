<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class HomeController 
{
    protected $renderer;
    
    public function __construct()
    {
        $this->renderer = new PhpRenderer('../templates');
    }
    
    /**
     * Display homepage
     */
    public function index(Request $request, Response $response)
    {
        return $this->renderer->render($response, 'home/index.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y')
        ]);
    }
    
    /**
     * Display user dashboard
     */
    public function dashboard(Request $request, Response $response)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            // Redirect to login if not logged in
            return $response->withHeader('Location', '/login')
                ->withStatus(302);
        }
        
        return $this->renderer->render($response, 'dashboard/index.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'user' => $_SESSION['user'] ?? []
        ]);
    }
    
    /**
     * Display user profile
     */
    public function profile(Request $request, Response $response)
    {
        // In a real app, we'd load the current user's profile
        $user = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'joined' => '2023-01-15'
        ];
        
        return $this->renderer->render($response, 'dashboard/profile.php', [
            'appName' => 'Gideons Technology',
            'currentYear' => date('Y'),
            'user' => $user
        ]);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(Request $request, Response $response)
    {
        // In a real app, we'd validate and update user profile
        
        // Redirect back to profile page
        return $response->withHeader('Location', '/dashboard/profile')
            ->withStatus(302);
    }
}
