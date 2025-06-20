<?php

namespace App\Controllers\Api\V1;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Services\ValidationService;
use App\Utilities\Logger;

/**
 * API User Controller
 * 
 * Handles all user-related API endpoints
 */
class UserController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var ValidationService
     */
    protected $validator;
    
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = new ValidationService();
    }
    
    /**
     * Get all users (admin only)
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            // In a real application, you would fetch users from the database
            // For demonstration, we'll return simulated data
            $users = [
                [
                    'id' => 1,
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'role' => 'admin',
                    'created_at' => '2023-01-01 00:00:00'
                ],
                [
                    'id' => 2,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'user',
                    'created_at' => '2023-01-02 00:00:00'
                ],
                [
                    'id' => 3,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'role' => 'user',
                    'created_at' => '2023-01-03 00:00:00'
                ]
            ];
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $users
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API UserController::index: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving users.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Get user by ID
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            // Get user ID from route arguments
            $id = (int) $args['id'];
            
            // In a real application, you would fetch the user from the database
            // For demonstration, we'll return simulated data
            $users = [
                1 => [
                    'id' => 1,
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'role' => 'admin',
                    'created_at' => '2023-01-01 00:00:00'
                ],
                2 => [
                    'id' => 2,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'user',
                    'created_at' => '2023-01-02 00:00:00'
                ],
                3 => [
                    'id' => 3,
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'role' => 'user',
                    'created_at' => '2023-01-03 00:00:00'
                ]
            ];
            
            // Check if user exists
            if (!isset($users[$id])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'User not found.'
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $users[$id]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API UserController::show({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while retrieving the user.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Update user
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            // Get user ID from route arguments
            $id = (int) $args['id'];
            
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['name', 'email']);
            
            // Validate email format
            if (isset($data['email'])) {
                $this->validator->email($data['email']);
            }
            
            // Check for validation errors
            if ($this->validator->hasErrors()) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'validation_error',
                        'message' => 'Validation failed',
                        'fields' => $this->validator->getErrors()
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
            }
            
            // In a real application, you would update the user in the database
            // For demonstration, we'll just return a success response
            
            $user = [
                'id' => $id,
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'] ?? 'user',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
                'message' => 'User updated successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API UserController::update({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while updating the user.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Delete user
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            // Get user ID from route arguments
            $id = (int) $args['id'];
            
            // In a real application, you would delete the user from the database
            // For demonstration, we'll just return a success response
            
            // Return JSON response
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error("Error in API UserController::delete({$args['id']}): " . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while deleting the user.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
