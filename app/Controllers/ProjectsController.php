<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class ProjectsController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->render($response, 'projects/index.php', [
            'title' => 'Our Projects - Gideon\'s Technology',
            'page' => 'projects'
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        
        // In a real app, you'd fetch the project from a database
        $project = [
            'id' => $id,
            'name' => 'Project ' . $id,
            'description' => 'Detailed description for project ' . $id
        ];
        
        return $this->render($response, 'projects/show.php', [
            'title' => $project['name'] . ' - Gideon\'s Technology',
            'page' => 'projects',
            'project' => $project
        ]);
    }
}