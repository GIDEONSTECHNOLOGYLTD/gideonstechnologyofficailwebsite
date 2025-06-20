<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

/**
 * API Controller
 * 
 * Handles API endpoints for the application
 */
class ApiController extends Controller
{
    /**
     * Get all templates
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getTemplates(Request $request, Response $response): Response
    {
        try {
            // Example data - in a real app, this would come from a database
            $templates = [
                [
                    'id' => 1,
                    'name' => 'E-commerce Template',
                    'description' => 'Complete e-commerce solution with payment integration',
                    'thumbnail' => 'ecommerce.jfif',
                    'category' => 'business',
                    'price' => 199.99
                ],
                [
                    'id' => 2,
                    'name' => 'Blog Template',
                    'description' => 'Professional blog template with multiple layouts',
                    'thumbnail' => 'landing.png',
                    'category' => 'blog',
                    'price' => 99.99
                ],
                [
                    'id' => 3,
                    'name' => 'School Management System',
                    'description' => 'Complete school management system with student and teacher portals',
                    'thumbnail' => 'schoolmanagement.jfif',
                    'category' => 'education',
                    'price' => 299.99
                ]
            ];
            
            return $this->json($response, [
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            // Log the error
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error fetching templates: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while fetching templates'
            ], 500);
        }
    }
    
    /**
     * Get all orders
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getOrders(Request $request, Response $response): Response
    {
        try {
            // Check if user is authenticated
            // This is a placeholder - you should implement proper authentication
            $isAuthenticated = isset($_SESSION['user_id']);
            
            if (!$isAuthenticated) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }
            
            // Example data - in a real app, this would come from a database
            $orders = [
                [
                    'id' => 101,
                    'user_id' => $_SESSION['user_id'] ?? 1,
                    'product_name' => 'E-commerce Template',
                    'total' => 199.99,
                    'status' => 'completed',
                    'date' => '2023-05-15'
                ],
                [
                    'id' => 102,
                    'user_id' => $_SESSION['user_id'] ?? 1,
                    'product_name' => 'School Management System',
                    'total' => 299.99,
                    'status' => 'processing',
                    'date' => '2023-06-20'
                ]
            ];
            
            return $this->json($response, [
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            // Log the error
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error fetching orders: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while fetching orders'
            ], 500);
        }
    }
    
    /**
     * Get all users
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getAllUsers(Request $request, Response $response): Response
    {
        try {
            // Check if user is admin
            // This is a placeholder - you should implement proper authorization
            $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
            
            if (!$isAdmin) {
                return $this->json($response, [
                    'success' => false,
                    'message' => 'Unauthorized access. Admin privileges required.'
                ], 403);
            }
            
            // Example data - in a real app, this would come from a database
            $users = [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'user',
                    'created_at' => '2023-01-15'
                ],
                [
                    'id' => 2,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'role' => 'admin',
                    'created_at' => '2023-02-20'
                ]
            ];
            
            return $this->json($response, [
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            // Log the error
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('Error fetching users: ' . $e->getMessage());
            }
            
            return $this->json($response, [
                'success' => false,
                'message' => 'An error occurred while fetching users'
            ], 500);
        }
    }
    
    public function getUserById(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $user = ['id' => $id, 'name' => 'User ' . $id, 'email' => 'user' . $id . '@example.com'];
        
        $payload = json_encode($user);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    
    public function createUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $newUser = [
            'id' => 3,
            'name' => $data['name'] ?? 'New User',
            'email' => $data['email'] ?? 'new@example.com'
        ];
        
        $payload = json_encode($newUser);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }
    
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $updatedUser = [
            'id' => $id,
            'name' => $data['name'] ?? 'Updated User',
            'email' => $data['email'] ?? 'updated@example.com'
        ];
        
        $payload = json_encode($updatedUser);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
    
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        return $response->withStatus(204);
    }
}