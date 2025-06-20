<?php

namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends ApiBaseController
{
    public function login(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validate required fields
            if (empty($data['email']) || empty($data['password'])) {
                return $this->error($response, 'Email and password are required', 400);
            }
            
            // Authentication logic would go here
            // For demo, we'll just assume successful login
            $token = bin2hex(random_bytes(16)); // Generate a sample token
            
            return $this->success($response, [
                'token' => $token,
                'user' => [
                    'id' => 1,
                    'name' => 'Sample User',
                    'email' => $data['email']
                ]
            ], 'Login successful');
            
        } catch (\Exception $e) {
            return $this->error($response, 'Authentication failed: ' . $e->getMessage(), 500);
        }
    }
    
    public function register(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validate required fields
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                return $this->error($response, 'Name, email and password are required', 400);
            }
            
            // Registration logic would go here
            // For demo, we'll just assume successful registration
            
            return $this->success($response, [
                'user' => [
                    'id' => 1,
                    'name' => $data['name'],
                    'email' => $data['email']
                ]
            ], 'Registration successful');
            
        } catch (\Exception $e) {
            return $this->error($response, 'Registration failed: ' . $e->getMessage(), 500);
        }
    }
    
    public function logout(Request $request, Response $response): Response
    {
        // Logout logic would go here
        return $this->success($response, [], 'Logout successful');
    }
}
