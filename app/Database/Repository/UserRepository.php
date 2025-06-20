<?php
/**
 * User Repository
 * 
 * Handles database operations for users
 */

namespace App\Database\Repository;

use App\Utilities\Logger;

class UserRepository extends BaseRepository
{
    /**
     * @var string The table name
     */
    protected $table = 'users';
    
    /**
     * Find a user by email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findOneBy('email', $email);
    }
    
    /**
     * Find a user by username
     * 
     * @param string $username
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        return $this->findOneBy('username', $username);
    }
    
    /**
     * Create a new user
     * 
     * @param array $userData
     * @return int|string User ID
     */
    public function createUser(array $userData)
    {
        // Hash password if provided
        if (isset($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }
        
        try {
            return $this->create($userData);
        } catch (\Exception $e) {
            Logger::error("Failed to create user: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update user details
     * 
     * @param int|string $userId
     * @param array $userData
     * @return bool
     */
    public function updateUser($userId, array $userData): bool
    {
        // Hash password if provided
        if (isset($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }
        
        return $this->update($userId, $userData);
    }
    
    /**
     * Validate user credentials
     * 
     * @param string $email
     * @param string $password
     * @return array|null User data if valid, null otherwise
     */
    public function validateCredentials(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Remove password from returned data
            unset($user['password']);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Get users with specific role
     * 
     * @param string $role
     * @return array
     */
    public function findByRole(string $role): array
    {
        return $this->findBy('role', $role);
    }
    
    /**
     * Check if email exists
     * 
     * @param string $email
     * @param int|null $excludeUserId User ID to exclude from check (for updates)
     * @return bool
     */
    public function emailExists(string $email, $excludeUserId = null): bool
    {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeUserId !== null) {
            $query .= " AND id != :id";
            $params[':id'] = $excludeUserId;
        }
        
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        
        return (int) $statement->fetchColumn() > 0;
    }
}
