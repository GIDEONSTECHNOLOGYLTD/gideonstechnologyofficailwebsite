<?php

namespace App\Http\Controllers\Admin;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            $users = User::all();
            
            return $this->render($response, 'admin/users/index.php', [
                'title' => 'Manage Users',
                'users' => $users,
                'active_menu' => 'users'
            ]);
        } catch (\Exception $e) {
            $this->container->get('logger')->error('User List Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to load users.');
            return $response->withHeader('Location', '/admin')->withStatus(302);
        }
    }

    /**
     * Show the form for creating a new user
     */
    public function create(Request $request, Response $response): Response
    {
        return $this->render($response, 'admin/users/create.php', [
            'title' => 'Create User',
            'active_menu' => 'users'
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        $validation = $this->validateUserData($data);
        
        if (!$validation['valid']) {
            $this->flash('error', implode(' ', $validation['errors']));
            return $response->withHeader('Location', '/admin/users/create')->withStatus(302);
        }
        
        try {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
            $user->role = $data['role'] ?? 'user';
            $user->save();
            
            $this->flash('success', 'User created successfully');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('User Create Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to create user. Email may already exist.');
            return $response->withHeader('Location', '/admin/users/create')->withStatus(302);
        }
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $user = User::findOrFail($args['id']);
            
            return $this->render($response, 'admin/users/show.php', [
                'title' => 'View User',
                'user' => $user,
                'active_menu' => 'users'
            ]);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('User Show Error: ' . $e->getMessage());
            $this->flash('error', 'User not found');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
        }
    }

    /**
     * Show the form for editing a user
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        try {
            $user = User::findOrFail($args['id']);
            
            return $this->render($response, 'admin/users/edit.php', [
                'title' => 'Edit User',
                'user' => $user,
                'active_menu' => 'users'
            ]);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('User Edit Error: ' . $e->getMessage());
            $this->flash('error', 'User not found');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        
        try {
            $user = User::findOrFail($args['id']);
            
            $validation = $this->validateUserData($data, $user->id);
            
            if (!$validation['valid']) {
                $this->flash('error', implode(' ', $validation['errors']));
                return $response->withHeader('Location', "/admin/users/{$user->id}/edit")->withStatus(302);
            }
            
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->role = $data['role'];
            
            if (!empty($data['password'])) {
                $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $user->save();
            
            $this->flash('success', 'User updated successfully');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('User Update Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to update user');
            return $response->withHeader('Location', "/admin/users/{$args['id']}/edit")->withStatus(302);
        }
    }

    /**
     * Remove the specified user
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $user = User::findOrFail($args['id']);
            
            // Prevent deleting own account
            if ($user->id === $this->container->get('session')->get('user_id')) {
                $this->flash('error', 'You cannot delete your own account');
                return $response->withHeader('Location', '/admin/users')->withStatus(302);
            }
            
            $user->delete();
            
            $this->flash('success', 'User deleted successfully');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
            
        } catch (\Exception $e) {
            $this->container->get('logger')->error('User Delete Error: ' . $e->getMessage());
            $this->flash('error', 'Failed to delete user');
            return $response->withHeader('Location', '/admin/users')->withStatus(302);
        }
    }
    
    /**
     * Validate user data
     */
    private function validateUserData(array $data, ?int $userId = null): array
    {
        $errors = [];
        
        $nameValidator = v::notEmpty()->length(2, 100);
        $emailValidator = v::notEmpty()->email();
        $passwordValidator = $userId ? v::optional(v::length(6, 100)) : v::notEmpty()->length(6, 100);
        $roleValidator = v::in(['admin', 'manager', 'user']);
        
        if (!$nameValidator->validate($data['name'] ?? '')) {
            $errors[] = 'Name must be between 2 and 100 characters';
        }
        
        if (!$emailValidator->validate($data['email'] ?? '')) {
            $errors[] = 'Valid email is required';
        } else {
            // Check for duplicate email
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser && $existingUser->id != $userId) {
                $errors[] = 'Email is already in use';
            }
        }
        
        if (empty($userId) || !empty($data['password'])) {
            if (!$passwordValidator->validate($data['password'] ?? '')) {
                $errors[] = 'Password must be at least 6 characters';
            }
        }
        
        if (!empty($data['role']) && !$roleValidator->validate($data['role'])) {
            $errors[] = 'Invalid role selected';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
