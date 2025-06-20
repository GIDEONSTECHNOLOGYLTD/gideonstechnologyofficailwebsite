<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    public function getAllUsers(Request $request, Response $response): Response
    {
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ];
        
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUserById(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $user = ['id' => $id, 'name' => 'User ' . $id, 'email' => 'user' . $id . '@example.com'];
        
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $user = ['id' => 3, 'name' => $data['name'] ?? 'New User', 'email' => $data['email'] ?? 'new@example.com'];
        
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json')
                       ->withStatus(201);
    }

    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $user = ['id' => $id, 'name' => $data['name'] ?? 'Updated User', 'email' => $data['email'] ?? 'updated@example.com'];
        
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        return $response->withStatus(204);
    }
}