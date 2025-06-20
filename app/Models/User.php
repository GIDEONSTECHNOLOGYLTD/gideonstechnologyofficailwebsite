<?php

namespace App\Models;

use App\Core\Security;
use PDO;

/**
 * User Model
 * Handles business logic related to users
 */
class User extends Model
{
    /**
     * Table name in database
     * @var string
     */
    protected $table = 'users';
    
    /**
     * Primary key column name
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Columns that can be filled via mass assignment
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'last_login',
        'avatar',
        'bio',
        'remember_token',
        'email_verified_at',
        'username'
    ];
    
    /**
     * Columns that should be hidden from array/JSON output
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];
    
    /**
     * Default column values
     * @var array
     */
    protected $defaults = [
        'role' => 'client',
        'status' => 'active'
    ];
    
    /**
     * Whether to use timestamps (created_at, updated_at)
     * @var bool
     */
    protected $timestamps = true;

    /**
     * Create a new user with registration logic
     * 
     * @param array $data User data
     * @return User|false User instance or false on failure
     */
    public function register(array $data)
    {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }
        
        // Create will automatically apply defaults and timestamps
        return $this->create($data);
    }
    
    /**
     * Find user by email
     * 
     * @param string $email User email
     * @return User|null User instance or null if not found
     */
    public function findByEmail(string $email)
    {
        return $this->findBy('email', $email);
    }
    
    /**
     * Find user by username
     * 
     * @param string $username Username
     * @return User|null User instance or null if not found
     */
    public function findByUsername(string $username)
    {
        return $this->findBy('username', $username);
    }
    
    /**
     * Authenticate user
     * 
     * @param string $email User email
     * @param string $password User password
     * @return User|false User instance or false if authentication fails
     */
    public function authenticate(string $email, string $password)
    {
        try {
            // Find user by email
            $user = $this->findByEmail($email);
            
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
        } catch (\Exception $e) {
            // Log error
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
        
        return false;
    }
    
    /**
     * Generate remember token
     * 
     * @param int $userId User ID
     * @return string Generated token
     */
    public function generateRememberToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        
        $this->update($userId, [
            'remember_token' => $token
            // updated_at will be automatically set by the Model class
        ]);
        
        return $token;
    }
    
    /**
     * Find user by remember token
     * 
     * @param string $token Remember token
     * @return User|null User instance or null if not found
     */
    public function findByRememberToken(string $token)
    {
        return $this->findBy('remember_token', $token);
    }
    
    /**
     * Change user password
     * 
     * @param int $userId User ID
     * @param string $password New password
     * @return bool Success status
     */
    public function changePassword(int $userId, string $password): bool
    {
        return $this->update($userId, [
            'password' => Security::hashPassword($password)
            // updated_at will be automatically set by the Model class
        ]);
    }
    
    /**
     * Verify if a password is correct for this user
     * 
     * @param string $password Password to verify
     * @return bool Whether password is correct
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
    
    /**
     * Get user orders
     * 
     * @param int $userId User ID
     * @param int $limit Number of orders to return
     * @return array List of orders
     */
    public function getOrders($userId, $limit = 10)
    {
        $query = "
            SELECT 
                o.*,
                s.name as service_name,
                s.slug as service_slug
            FROM orders o
            LEFT JOIN services s ON o.service_id = s.id
            WHERE o.user_id = :user_id
            ORDER BY o.created_at DESC
            LIMIT :limit
        ";
        
        $stmt = Database::getConnection()->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Check if user has role
     * 
     * @param int $userId User ID
     * @param string $role Role to check
     * @return bool Has role
     */
    public function hasRole($userId, $role)
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Convert array to object if needed
        if (is_array($user)) {
            $user = (object) $user;
        }
        
        return $user->role === $role;
    }
    
    /**
     * Change user status
     * 
     * @param int $userId User ID
     * @param string $status New status
     * @return bool Success status
     */
    public function changeStatus($userId, $status)
    {
        return $this->update($userId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Create a new user
     * 
     * @param array $data User data
     * @return int|bool The new user ID on success, false on failure
     */
    public function create(array $data)
    {
        // Hash password if not already hashed
        if (isset($data['password']) && strlen($data['password']) < 60) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Default timestamps if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return parent::create($data);
    }
    
    /**
     * Get a user by ID
     * 
     * @param int $id User ID
     * @return object|false User object or false if not found
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    // Authentication methods
    public function login(string $email, string $password)
    {
        return $this->authenticate($email, $password);
    }

    // Two-factor authentication methods
    public function setupTwoFactor() {
        // Generate a new 2FA secret
        $this->twoFASecret = $this->generateSecret();
        return $this->twoFASecret;
    }

    public function verifyAndEnableTwoFactor($code) {
        // Verify the code against the secret
        $verified = $this->verifyCode($code, $this->twoFASecret);
        if ($verified) {
            $this->enable2FA();
            return true;
        }
        return false;
    }

    public function update2FASecret($secret) {
        $this->twoFASecret = $secret;
        return true;
    }

    public function enable2FA() {
        $this->twoFAEnabled = true;
        return true;
    }

    public function disable2FA() {
        $this->twoFAEnabled = false;
        $this->twoFASecret = null;
        return true;
    }

    // Getter methods
    public function getEmail() {
        return $this->email;
    }

    public function get2FASecret() {
        return $this->twoFASecret;
    }

    public function is2FAEnabled() {
        return $this->twoFAEnabled;
    }

    // Helper methods
    private function generateSecret() {
        // In a real implementation, use a proper library for generating secure secrets
        return bin2hex(random_bytes(16));
    }

    private function verifyCode($code, $secret) {
        // In a real implementation, use a proper TOTP verification library
        // This is just a placeholder
        return strlen($code) === 6 && is_numeric($code);
    }

    /**
     * Count all users
     * 
     * @return int Total number of users
     */
    public function countAll(): int
    {
        return $this->count();
    }
    
    /**
     * Get recent users
     * 
     * @param int $limit Number of users to return
     * @return array Recent users
     */
    public function getRecent(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Update user password
     * 
     * @param int $userId User ID
     * @param string $password New password (plain text)
     * @return bool Success status
     */
    public function updatePassword(int $userId, string $password): bool
    {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update the password in the database
        $data = [
            'password' => $hashedPassword,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($userId, $data);
    }
    
    /**
     * Find top users by order value
     * 
     * @param int $limit Number of users to return
     * @return array Top users by order value
     */
    public function findTopByOrderValue(int $limit = 20): array
    {
        $sql = "SELECT u.*, COALESCE(SUM(o.total), 0) as total_spent, COUNT(o.id) as order_count
                FROM {$this->table} u
                LEFT JOIN orders o ON u.id = o.user_id AND o.status IN ('completed', 'shipped')
                GROUP BY u.id
                ORDER BY total_spent DESC, u.id ASC
                LIMIT :limit";
                
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Find user by ID with optional throw on failure
     * 
     * @param int $id User ID
     * @return object User object
     * @throws \Exception If user not found
     */
    public function findOrFail(int $id): object
    {
        $user = $this->find($id);
        
        if (!$user) {
            throw new \Exception("User with ID {$id} not found");
        }
        
        return (object) $user;
    }

    /**
     * Find user by ID
     * 
     * @param int $id User ID
     * @return User|null
     */
    public function findById($id)
    {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $params = [':id' => $id];
        
        $result = Database::query($query, $params);
        
        if ($result && !empty($result)) {
            return $this->mapResultToUser($result[0]);
        }
        
        return null;
    }
    
    /**
     * Maps a database result to a User object
     * 
     * @param array $userData User data from database
     * @return User
     */
    private function mapResultToUser($userData)
    {
        $user = new self();
        
        foreach ($userData as $key => $value) {
            if (property_exists($user, $key)) {
                $user->$key = $value;
            }
        }
        
        return $user;
    }

    /**
     * Count records in the table
     * 
     * @param string $whereClause Optional WHERE clause
     * @param array $params Parameters for the WHERE clause
     * @return int Number of records
     */
    public function count($whereClause = '', array $params = []): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM users";
            
            if ($whereClause) {
                $sql .= " WHERE {$whereClause}";
            }
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Database error in User::count(): {$e->getMessage()}");
            return 0;
        }
    }

    /**
     * Update method that ensures type compatibility with parent class
     * 
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        return parent::update($id, $data);
    }
}
