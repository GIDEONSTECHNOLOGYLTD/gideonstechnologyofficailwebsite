<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class PageController
{
    private $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function home(Request $request, Response $response): Response
    {
        return $this->renderer->render($response, 'home.php', [
            'title' => 'Gideon\'s Technology',
            'page' => 'home'
        ]);
    }

    public function about(Request $request, Response $response): Response
    {
        return $this->renderer->render($response, 'about.php', [
            'title' => 'About Us - Gideon\'s Technology',
            'page' => 'about'
        ]);
    }

    public function contact(Request $request, Response $response): Response
    {
        return $this->renderer->render($response, 'contact.php', [
            'title' => 'Contact Us - Gideon\'s Technology',
            'page' => 'contact'
        ]);
    }
}