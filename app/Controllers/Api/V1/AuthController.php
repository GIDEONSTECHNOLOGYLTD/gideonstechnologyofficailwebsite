<?php

namespace App\Controllers\Api\V1;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Core\JWT;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\ValidationService;
use App\Utilities\Logger;

/**
 * API Authentication Controller
 * 
 * Handles all authentication-related API endpoints
 */
class AuthController
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
     * @var UserRepository
     */
    protected $userRepository;
    
    /**
     * @var JWT
     */
    protected $jwt;
    
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = new ValidationService();
        $this->userRepository = new UserRepository();
        
        // Get JWT secret from config (in a real app, this would be in a secure config)
        $jwtSecret = getenv('JWT_SECRET') ?: 'your-secret-key';
        $this->jwt = new JWT($jwtSecret);
    }
    
    /**
     * Login endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function login(Request $request, Response $response): Response
    {
        try {
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['email', 'password']);
            
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
            
            // In a real application, you would verify credentials against a database
            // For demonstration, we'll use a simple check
            $email = $data['email'];
            $password = $data['password'];
            
            // Authenticate user with repository
            $user = $this->userRepository->authenticate($email, $password);
            
            if ($user) {
                // Generate JWT token
                $token = $this->jwt->generate([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);
                
                // Return success response with token
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'data' => [
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'expires_in' => 3600,
                        'user' => [
                            'id' => $user->id,
                            'email' => $user->email,
                            'name' => $user->name,
                            'role' => $user->role
                        ]
                    ]
                ]));
                
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            // Authentication failed
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'invalid_credentials',
                    'message' => 'Invalid email or password'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API AuthController::login: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred during login.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Registration endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response): Response
    {
        try {
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['name', 'email', 'password', 'password_confirm']);
            
            // Validate email format
            if (isset($data['email'])) {
                $this->validator->email($data['email']);
            }
            
            // Validate password length
            if (isset($data['password'])) {
                $this->validator->minLength($data['password'], 8, 'password');
            }
            
            // Validate password confirmation
            if (isset($data['password']) && isset($data['password_confirm'])) {
                $this->validator->matches($data['password'], $data['password_confirm'], 'password_confirm');
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
            
            // Remove password confirmation from data before creating user
            unset($data['password_confirm']);
            
            // Register user with repository
            $user = $this->userRepository->register($data);
            
            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 'registration_failed',
                        'message' => 'Failed to register user. Email may already be in use.'
                    ]
                ]));
                
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
            }
            
            // Generate JWT token
            $token = $this->jwt->generate([
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);
            
            // Return success response
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 3600,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ]
                ]
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API AuthController::register: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred during registration.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Logout endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request, Response $response): Response
    {
        // In a real application, you would invalidate the token
        // For demonstration, we'll just return a success response
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Successfully logged out'
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    /**
     * Get current user information
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function me(Request $request, Response $response): Response
    {
        // In a real application, you would get the user information from the authenticated token
        // For demonstration, we'll return a simulated user
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin'
            ]
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    /**
     * Forgot password endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function forgotPassword(Request $request, Response $response): Response
    {
        try {
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['email']);
            
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
            
            // In a real application, you would send a password reset email
            // For demonstration, we'll just return a success response
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Password reset instructions sent to your email'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API AuthController::forgotPassword: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while processing your request.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Reset password endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function resetPassword(Request $request, Response $response): Response
    {
        try {
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['token', 'password', 'password_confirm']);
            
            // Validate password length
            if (isset($data['password'])) {
                $this->validator->minLength($data['password'], 8, 'password');
            }
            
            // Validate password confirmation
            if (isset($data['password']) && isset($data['password_confirm'])) {
                $this->validator->matches($data['password'], $data['password_confirm'], 'password_confirm');
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
            
            // In a real application, you would verify the token and update the user's password
            // For demonstration, we'll just return a success response
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Password has been reset successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API AuthController::resetPassword: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while processing your request.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
    
    /**
     * Change password endpoint
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function changePassword(Request $request, Response $response): Response
    {
        try {
            // Get request data
            $data = $request->getParsedBody();
            
            // Sanitize input
            $data = $this->validator->sanitize($data);
            
            // Validate required fields
            $this->validator->required($data, ['current_password', 'new_password', 'new_password_confirm']);
            
            // Validate password length
            if (isset($data['new_password'])) {
                $this->validator->minLength($data['new_password'], 8, 'new_password');
            }
            
            // Validate password confirmation
            if (isset($data['new_password']) && isset($data['new_password_confirm'])) {
                $this->validator->matches($data['new_password'], $data['new_password_confirm'], 'new_password_confirm');
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
            
            // In a real application, you would verify the current password and update to the new password
            // For demonstration, we'll just return a success response
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Password has been changed successfully'
            ]));
            
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error
            Logger::error('Error in API AuthController::changePassword: ' . $e->getMessage());
            
            // Return error response
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'server_error',
                    'message' => 'An error occurred while processing your request.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}
