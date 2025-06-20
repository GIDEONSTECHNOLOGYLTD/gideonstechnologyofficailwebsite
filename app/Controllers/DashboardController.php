<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class DashboardController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, 'dashboard.php', [
            'title' => 'Dashboard - Gideon\'s Technology',
            'page' => 'dashboard'
        ]);
    }
}
