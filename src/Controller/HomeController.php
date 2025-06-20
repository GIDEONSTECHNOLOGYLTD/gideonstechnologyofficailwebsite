<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    public function index(Request $request, Response $response): Response
    {
        $response->getBody()->write('Welcome to Gideon\'s Technology');
        return $response;
    }

    public function about(Request $request, Response $response): Response
    {
        $response->getBody()->write('About Gideon\'s Technology');
        return $response;
    }

    public function contact(Request $request, Response $response): Response
    {
        $response->getBody()->write('Contact Gideon\'s Technology');
        return $response;
    }
}