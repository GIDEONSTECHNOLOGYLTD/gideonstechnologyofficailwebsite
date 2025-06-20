<?php

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utilities\Logger;

class UserController
{
    protected $container;
    protected $renderer;
    protected $db;
    
    /**
     * Constructor
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->renderer = $container->get('renderer');
        $this->db = $container->get('db');
    }
    
    /**
     * Display list of users
     */
    public function index(Request $request, Response $response): Response
    {
        // Get all users from database
        $stmt = $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
        
        return $this->renderer->render($response, 'admin/users/index.php', [
            'title' => 'Manage Users',
            'users' => $users
        ]);
    }
    
    /**
     * Display user creation form
     */
    public function create(Request $request, Response $response): Response
    {
        return $this->renderer->render($response, 'admin/users/create.php', [
            'title' => 'Create User'
        ]);
    }
    
    /**
     * Store a new user
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        $errors = [];
        if (empty($data['name'])) $errors['name'] = 'Name is required';
        if (empty($data['email'])) $errors['email'] = 'Email is required';
        if (empty($data['password'])) $errors['password'] = 'Password is required';
        if (empty($data['role'])) $errors['role'] = 'Role is required';
        
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();
        if ($stmt->fetch()) {
            $errors['email'] = 'Email already exists';
        }
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            return $this->renderer->render($response, 'admin/users/create.php', [
                'title' => 'Create User',
                'errors' => $errors,
                'data' => $data
            ]);
        }
        
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, created_at, updated_at) 
             VALUES (:name, :email, :password, :role, NOW(), NOW())"
        );
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $data['role']);
        $stmt->execute();
        
        // Log the action
        Logger::info("Admin created new user: {$data['email']}");
        
        // Redirect with success message
        $this->container->get('flash')->addMessage('success', 'User created successfully');
        return $response->withHeader('Location', '/admin/users')->withStatus(302);
    }
    
    /**
     * Display user edit form
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        
        // Get user from database
        $stmt = $this->db->prepare("SELECT id, name, email, role FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if (!$user) {
            $this->container->get('flash')->addMessage('error', 'User not found');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
        }
        
        return $this->renderer->render($response, 'admin/users/edit.php', [
            'title' => 'Edit User',
            'user' => $user
        ]);
    }
    
    /**
     * Update user
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        // Validate input
        $errors = [];
        if (empty($data['name'])) $errors['name'] = 'Name is required';
        if (empty($data['email'])) $errors['email'] = 'Email is required';
        if (empty($data['role'])) $errors['role'] = 'Role is required';
        
        // Check if email already exists for another user
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetch()) {
            $errors['email'] = 'Email already exists';
        }
        
        // If validation fails, return to form with errors
        if (!empty($errors)) {
            return $this->renderer->render($response, 'admin/users/edit.php', [
                'title' => 'Edit User',
                'errors' => $errors,
                'user' => array_merge(['id' => $id], $data)
            ]);
        }
        
        // Update user
        if (!empty($data['password'])) {
            // If password was provided, update it too
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare(
                "UPDATE users SET name = :name, email = :email, password = :password, 
                 role = :role, updated_at = NOW() WHERE id = :id"
            );
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            // Otherwise just update other fields
            $stmt = $this->db->prepare(
                "UPDATE users SET name = :name, email = :email, role = :role, 
                 updated_at = NOW() WHERE id = :id"
            );
        }
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Log the action
        Logger::info("Admin updated user: {$data['email']}");
        
        // Redirect with success message
        $this->container->get('flash')->addMessage('success', 'User updated successfully');
        return $response->withHeader('Location', '/admin/users')->withStatus(302);
    }
    
    /**
     * Delete user
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        
        // Get user email for logging
        $stmt = $this->db->prepare("SELECT email FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if (!$user) {
            $this->container->get('flash')->addMessage('error', 'User not found');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
        }
        
        // Delete user
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Log the action
        Logger::info("Admin deleted user: {$user['email']}");
        
        // Redirect with success message
        $this->container->get('flash')->addMessage('success', 'User deleted successfully');
        return $response->withHeader('Location', '/admin/users')->withStatus(302);
    }
}
