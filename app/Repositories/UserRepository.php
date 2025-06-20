<?php

namespace App\Repositories;

use App\Models\User;
use App\Core\Security;

/**
 * User Repository
 * 
 * Implements the repository pattern for User model
 */
class UserRepository extends Repository
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(new User());
    }
    
    /**
     * Register a new user
     * 
     * @param array $data User data
     * @return User|false User model or false on failure
     */
    public function register(array $data)
    {
        // Validate data before registration
        if (!isset($data['email']) || !isset($data['password'])) {
            return false;
        }
        
        // Hash password
        $data['password'] = Security::hashPassword($data['password']);
        
        return $this->create($data);
    }
    
    /**
     * Authenticate a user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return User|false User model or false on failure
     */
    public function authenticate(string $email, string $password)
    {
        // Find user by email
        $user = $this->findOneBy('email', $email);
        
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (password_verify($password, $user->password)) {
            // Update last login timestamp
            $this->update($user->id, [
                'last_login' => date('Y-m-d H:i:s')
            ]);
            
            return $user;
        }
        
        return false;
    }
    
    /**
     * Generate a remember token for a user
     * 
     * @param int $userId User ID
     * @return string|false Generated token or false on failure
     */
    public function generateRememberToken(int $userId)
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }
        
        $token = bin2hex(random_bytes(32));
        
        $updated = $this->update($userId, [
            'remember_token' => $token
        ]);
        
        return $updated ? $token : false;
    }
    
    /**
     * Find a user by remember token
     * 
     * @param string $token Remember token
     * @return User|null User model or null if not found
     */
    public function findByRememberToken(string $token)
    {
        return $this->findOneBy('remember_token', $token);
    }
    
    /**
     * Change a user's password
     * 
     * @param int $userId User ID
     * @param string $password New password
     * @return bool Success status
     */
    public function changePassword(int $userId, string $password): bool
    {
        return $this->update($userId, [
            'password' => Security::hashPassword($password)
        ]);
    }
    
    /**
     * Get active users
     * 
     * @param int $limit Maximum number of users to return
     * @return array Array of User models
     */
    public function getActiveUsers(int $limit = 10): array
    {
        return $this->findBy(['status' => 'active'], 'AND');
    }
    
    /**
     * Get users by role
     * 
     * @param string $role Role name
     * @return array Array of User models
     */
    public function getUsersByRole(string $role): array
    {
        return $this->findBy(['role' => $role], 'AND');
    }
    
    /**
     * Search users by name or email
     * 
     * @param string $query Search query
     * @return array Array of User models
     */
    public function searchUsers(string $query): array
    {
        $sql = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY name ASC";
        $stmt = $this->raw($sql, ["%{$query}%", "%{$query}%"]);
        
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $users = [];
        
        foreach ($results as $result) {
            $user = new User();
            $users[] = $user->hydrate($result);
        }
        
        return $users;
    }
}
