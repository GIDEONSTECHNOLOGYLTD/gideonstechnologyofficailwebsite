<?php

namespace App\Controllers;

use App\Core\TwoFactorAuth;
use App\Core\CsrfProtection;
use App\Core\Logger;
use App\Models\ServiceRequest;
use App\Models\ServiceHistory;
use App\Models\Consultation; 
use App\Models\Service;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    protected $db;
    protected $flash;
    protected $renderer;
    
    public function __construct($container)
    {
        $this->db = $container->get('db');
        $this->flash = $container->get('flash');
        $this->renderer = $container->get('renderer');
    }
    
    /**
     * Display user dashboard with service request summary
     */
    public function dashboard(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Get user details
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Get recent orders (if table exists)
            $recentOrders = [];
            try {
                $stmt = $this->db->prepare("
                    SELECT * FROM orders 
                    WHERE user_id = :user_id 
                    ORDER BY created_at DESC 
                    LIMIT 5
                ");
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();
                $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                // Orders table might not exist yet
                Logger::debug('Orders table not found: ' . $e->getMessage());
            }
            
            // Get recent activity
            $recentActivity = [];
            try {
                $stmt = $this->db->prepare("
                    SELECT * FROM user_activity_logs 
                    WHERE user_id = :user_id 
                    ORDER BY created_at DESC 
                    LIMIT 10
                ");
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();
                $recentActivity = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                // Activity logs table might not exist yet
                Logger::debug('Activity logs table not found: ' . $e->getMessage());
            }
            
            return $this->renderer->render($response, 'user/dashboard.php', [
                'title' => 'Dashboard',
                'user' => $user,
                'recentOrders' => $recentOrders,
                'recentActivity' => $recentActivity
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Dashboard error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while loading your dashboard');
            return $response->withHeader('Location', '/')->withStatus(302);
        }
    }
    
    /**
     * Display user profile
     */
    public function profile(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Get user details
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $this->renderer->render($response, 'user/profile.php', [
                'title' => 'My Profile',
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Profile error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while loading your profile');
            return $response->withHeader('Location', '/user/dashboard')->withStatus(302);
        }
    }
    
    /**
     * Display user settings page
     */
    public function settings(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Get user details
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $this->renderer->render($response, 'user/settings.php', [
                'title' => 'Account Settings',
                'user' => $user,
                'appName' => 'Gideons Technology',
                'currentYear' => date('Y')
            ]);
            
        } catch (\Exception $e) {
            Logger::error('Settings page error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while loading your settings');
            return $response->withHeader('Location', '/user/dashboard')->withStatus(302);
        }
    }
    
    /**
     * Update user settings
     */
    public function updateSettings(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $data = $request->getParsedBody();
        
        // Validate CSRF token
        if (!CsrfProtection::validateToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid security token');
            return $response->withHeader('Location', '/user/settings')->withStatus(302);
        }
        
        try {
            // Prepare settings data
            $settings = [
                'email_notifications' => isset($data['email_notifications']) ? 1 : 0,
                'marketing_emails' => isset($data['marketing_emails']) ? 1 : 0,
                'data_collection' => isset($data['data_collection']) ? 1 : 0,
                'third_party_sharing' => isset($data['third_party_sharing']) ? 1 : 0,
                'theme_preference' => $data['theme_preference'] ?? 'system',
                'language_preference' => $data['language_preference'] ?? 'en'
            ];
            
            // Update user settings
            $updateFields = [];
            $updateParams = [];
            
            foreach ($settings as $key => $value) {
                $updateFields[] = "{$key} = :{$key}";
                $updateParams[":{$key}"] = $value;
            }
            
            $updateParams[':id'] = $userId;
            
            $stmt = $this->db->prepare("UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id");
            foreach ($updateParams as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->execute();
            
            // Log activity
            $this->logUserActivity($userId, 'Updated account settings');
            
            $this->flash->addMessage('success', 'Settings updated successfully');
            return $response->withHeader('Location', '/user/settings')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Settings update error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while updating your settings');
            return $response->withHeader('Location', '/user/settings')->withStatus(302);
        }
    }
    
    /**
     * Update user profile
     */
    public function updateProfile(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $data = $request->getParsedBody();
        
        // Validate CSRF token
        if (!CsrfProtection::validateToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid security token');
            return $response->withHeader('Location', '/user/profile')->withStatus(302);
        }
        
        try {
            // Get current user data
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new \Exception('User not found');
            }
            
            // Validate email uniqueness if changed
            if ($data['email'] !== $user['email']) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
                $stmt->bindParam(':email', $data['email']);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                
                if ($stmt->fetchColumn() > 0) {
                    $this->flash->addMessage('error', 'Email address is already in use');
                    return $response->withHeader('Location', '/user/profile')->withStatus(302);
                }
            }
            
            // Handle password change if requested
            $passwordChanged = false;
            if (!empty($data['current_password']) && !empty($data['new_password'])) {
                // Verify current password
                if (!password_verify($data['current_password'], $user['password'])) {
                    $this->flash->addMessage('error', 'Current password is incorrect');
                    return $response->withHeader('Location', '/user/profile')->withStatus(302);
                }
                
                // Validate new password
                if (strlen($data['new_password']) < 8) {
                    $this->flash->addMessage('error', 'New password must be at least 8 characters long');
                    return $response->withHeader('Location', '/user/profile')->withStatus(302);
                }
                
                // Confirm passwords match
                if ($data['new_password'] !== $data['confirm_password']) {
                    $this->flash->addMessage('error', 'New passwords do not match');
                    return $response->withHeader('Location', '/user/profile')->withStatus(302);
                }
                
                // Hash new password
                $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
                $passwordChanged = true;
            }
            
            // Update user data
            $sql = "UPDATE users SET 
                    name = :name, 
                    email = :email";
            
            // Check if phone field exists in the form and database
            if (isset($data['phone']) && array_key_exists('phone', $user)) {
                $sql .= ", phone = :phone";
            }
            
            // Check if address fields exist in the form and database
            if (isset($data['address']) && array_key_exists('address', $user)) {
                $sql .= ", address = :address";
            }
            
            if (isset($data['city']) && array_key_exists('city', $user)) {
                $sql .= ", city = :city";
            }
            
            if (isset($data['country']) && array_key_exists('country', $user)) {
                $sql .= ", country = :country";
            }
            
            if ($passwordChanged) {
                $sql .= ", password = :password";
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            
            // Bind optional parameters if they exist
            if (isset($data['phone']) && array_key_exists('phone', $user)) {
                $stmt->bindParam(':phone', $data['phone']);
            }
            
            if (isset($data['address']) && array_key_exists('address', $user)) {
                $stmt->bindParam(':address', $data['address']);
            }
            
            if (isset($data['city']) && array_key_exists('city', $user)) {
                $stmt->bindParam(':city', $data['city']);
            }
            
            if (isset($data['country']) && array_key_exists('country', $user)) {
                $stmt->bindParam(':country', $data['country']);
            }
            
            if ($passwordChanged) {
                $stmt->bindParam(':password', $hashedPassword);
            }
            
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            // Log the activity
            $this->logUserActivity($userId, 'Updated profile information');
            
            // Update session data
            $_SESSION['user']['name'] = $data['name'];
            $_SESSION['user']['email'] = $data['email'];
            
            $this->flash->addMessage('success', 'Profile updated successfully');
            return $response->withHeader('Location', '/user/profile')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Profile update error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while updating your profile');
            return $response->withHeader('Location', '/user/profile')->withStatus(302);
        }
    }
    
    /**
     * Display two-factor authentication setup page
     */
    public function twoFactorSetup(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Get user details
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $twoFactorEnabled = (bool)($user['two_factor_enabled'] ?? false);
            $recoveryCodes = [];
            $secret = null;
            $qrCodeUrl = null;
            
            // If 2FA is already enabled, get recovery codes
            if ($twoFactorEnabled) {
                try {
                    $stmt = $this->db->prepare("
                        SELECT code FROM two_factor_recovery_codes 
                        WHERE user_id = :user_id AND used = 0
                    ");
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->execute();
                    $recoveryCodes = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                } catch (\Exception $e) {
                    // Recovery codes table might not exist yet
                    Logger::debug('Recovery codes table not found: ' . $e->getMessage());
                }
            } else {
                // Generate new secret for setup
                $twoFactorAuth = new TwoFactorAuth();
                $secret = $twoFactorAuth->generateSecret();
                $qrCodeUrl = $twoFactorAuth->getQRCodeUrl(
                    $_SESSION['user']['email'],
                    $secret,
                    'Gideon\'s Technology'
                );
            }
            
            return $this->renderer->render($response, 'user/two_factor_setup.php', [
                'title' => 'Two-Factor Authentication',
                'user' => $user,
                'twoFactorEnabled' => $twoFactorEnabled,
                'secret' => $secret,
                'qrCodeUrl' => $qrCodeUrl,
                'recoveryCodes' => $recoveryCodes
            ]);
            
        } catch (\Exception $e) {
            Logger::error('2FA setup error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while loading the 2FA setup page');
            return $response->withHeader('Location', '/user/dashboard')->withStatus(302);
        }
    }
    
    /**
     * Enable two-factor authentication
     */
    public function enableTwoFactor(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $data = $request->getParsedBody();
        
        // Validate CSRF token
        if (!CsrfProtection::validateToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid security token');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
        }
        
        try {
            $secret = $data['secret'] ?? '';
            $code = $data['code'] ?? '';
            
            if (empty($secret) || empty($code)) {
                throw new \Exception('Missing required fields');
            }
            
            // Verify the code
            $twoFactorAuth = new TwoFactorAuth();
            $verified = $twoFactorAuth->verifyCode($secret, $code);
            
            if (!$verified) {
                $this->flash->addMessage('error', 'Invalid verification code. Please try again.');
                return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
            }
            
            // Update user record to enable 2FA
            $stmt = $this->db->prepare("
                UPDATE users 
                SET two_factor_enabled = 1, two_factor_secret = :secret 
                WHERE id = :id
            ");
            $stmt->bindParam(':secret', $secret);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            // Generate recovery codes
            $recoveryCodes = $this->generateRecoveryCodes($userId);
            
            // Log the activity
            $this->logUserActivity($userId, 'Enabled two-factor authentication');
            
            $this->flash->addMessage('success', 'Two-factor authentication has been enabled successfully');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Enable 2FA error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while enabling two-factor authentication');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
        }
    }
    
    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $data = $request->getParsedBody();
        
        // Validate CSRF token
        if (!CsrfProtection::validateToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid security token');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
        }
        
        try {
            // Update user record to disable 2FA
            $stmt = $this->db->prepare("
                UPDATE users 
                SET two_factor_enabled = 0, two_factor_secret = NULL 
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            // Delete recovery codes if table exists
            try {
                $stmt = $this->db->prepare("DELETE FROM two_factor_recovery_codes WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();
            } catch (\Exception $e) {
                // Recovery codes table might not exist yet
                Logger::debug('Recovery codes table not found: ' . $e->getMessage());
            }
            
            // Log the activity
            $this->logUserActivity($userId, 'Disabled two-factor authentication');
            
            $this->flash->addMessage('success', 'Two-factor authentication has been disabled');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Disable 2FA error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while disabling two-factor authentication');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
        }
    }
    
    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            // Delete existing recovery codes
            $stmt = $this->db->prepare("DELETE FROM two_factor_recovery_codes WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            // Generate new recovery codes
            $this->generateRecoveryCodes($userId);
            
            // Log the activity
            $this->logUserActivity($userId, 'Regenerated two-factor recovery codes');
            
            $this->flash->addMessage('success', 'Recovery codes have been regenerated');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Regenerate recovery codes error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while regenerating recovery codes');
            return $response->withHeader('Location', '/user/two-factor')->withStatus(302);
        }
    }
    
    /**
     * Generate recovery codes for a user
     * 
     * @param int $userId
     * @return array
     */
    private function generateRecoveryCodes(int $userId): array
    {
        $recoveryCodes = [];
        
        // Generate 8 recovery codes
        for ($i = 0; $i < 8; $i++) {
            $code = $this->generateRandomString(10);
            $recoveryCodes[] = $code;
            
            // Insert into database
            $stmt = $this->db->prepare("
                INSERT INTO two_factor_recovery_codes (user_id, code) 
                VALUES (:user_id, :code)
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':code', $code);
            $stmt->execute();
        }
        
        return $recoveryCodes;
    }
    
    /**
     * Generate a random string
     * 
     * @param int $length
     * @return string
     */
    private function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    /**
     * Log user activity
     * 
     * @param int $userId
     * @param string $description
     * @return void
     */
    private function logUserActivity(int $userId, string $description): void
    {
        try {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_logs (user_id, description, ip_address, user_agent) 
                VALUES (:user_id, :description, :ip_address, :user_agent)
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':ip_address', $ipAddress);
            $stmt->bindParam(':user_agent', $userAgent);
            $stmt->execute();
        } catch (\Exception $e) {
            Logger::error('Activity logging error: ' . $e->getMessage());
        }
    }
    
    /**
     * API methods for backward compatibility
     */
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
    
    /**
     * Service Requests Section
     * Methods to handle user service requests
     */
    
    /**
     * Display all service requests for the logged-in user
     */
    public function serviceRequests(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $serviceRequestModel = new ServiceRequest($this->db);
        $serviceRequests = $serviceRequestModel->getAllByUserId($userId);
        
        // Render template with service requests
        return $this->renderer->render($response, 'user/service-requests.php', [
            'title' => 'My Service Requests',
            'serviceRequests' => $serviceRequests,
        ]);
    }
    
    /**
     * Display details of a single service request including related project and milestones
     */
    public function serviceRequestDetail(Request $request, Response $response, array $args): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $serviceRequestId = (int)$args['id'];
        
        $serviceRequestModel = new ServiceRequest($this->db);
        $serviceRequest = $serviceRequestModel->getById($serviceRequestId);
        
        // Check if service request exists and belongs to current user
        if (!$serviceRequest || $serviceRequest['user_id'] != $userId) {
            $this->flash->addMessage('error', 'Service request not found or access denied.');
            return $response->withHeader('Location', '/user/service-requests')->withStatus(302);
        }
        
        // Get project details if linked to this service request
        $project = [];
        if (!empty($serviceRequest['project_id'])) {
            try {
                $stmt = $this->db->prepare(
                    "SELECT p.*, 
                    (SELECT COUNT(*) FROM project_milestones WHERE project_id = p.id) as milestone_count 
                    FROM projects p WHERE p.id = :project_id"
                );
                $stmt->bindParam(':project_id', $serviceRequest['project_id']);
                $stmt->execute();
                $project = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                // Get milestones
                if ($project) {
                    $stmt = $this->db->prepare(
                        "SELECT * FROM project_milestones 
                        WHERE project_id = :project_id 
                        ORDER BY due_date ASC"
                    );
                    $stmt->bindParam(':project_id', $serviceRequest['project_id']);
                    $stmt->execute();
                    $project['milestones'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }
            } catch (\PDOException $e) {
                // Log the error
                Logger::error('Error fetching project: ' . $e->getMessage());
            }
        }
        
        // Get service updates
        $serviceUpdates = [];
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM service_updates 
                WHERE service_request_id = :service_request_id 
                ORDER BY created_at DESC"
            );
            $stmt->bindParam(':service_request_id', $serviceRequestId);
            $stmt->execute();
            $serviceUpdates = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log the error but continue
            Logger::error('Error fetching service updates: ' . $e->getMessage());
        }
        
        // Generate CSRF token for cancel form
        $csrfToken = CsrfProtection::generateToken();
        
        // Render template with service request details
        return $this->renderer->render($response, 'user/service-request-detail.php', [
            'title' => 'Service Request: ' . $serviceRequest['title'],
            'serviceRequest' => $serviceRequest,
            'project' => $project,
            'serviceUpdates' => $serviceUpdates,
            'csrfToken' => $csrfToken,
        ]);
    }
    
    /**
     * Show form for creating a new service request
     */
    public function newServiceRequestForm(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Get all available services
        $services = [];
        try {
            $stmt = $this->db->query("SELECT * FROM services WHERE active = 1 ORDER BY name ASC");
            $services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log the error but continue
            Logger::error('Error fetching services: ' . $e->getMessage());
        }
        
        // Generate CSRF token for form
        $csrfToken = CsrfProtection::generateToken();
        
        // Render template with the form
        return $this->renderer->render($response, 'user/new-service-request.php', [
            'title' => 'Create Service Request',
            'services' => $services,
            'csrfToken' => $csrfToken,
            'formData' => $_SESSION['form_data'] ?? [],
            'errors' => $_SESSION['form_errors'] ?? [],
        ]);
    }
    
    /**
     * Create a new service request
     */
    public function createServiceRequest(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!CsrfProtection::verifyToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid form submission, please try again.');
            return $response->withHeader('Location', '/user/new-service-request')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $title = $data['title'] ?? '';
        $serviceId = $data['service_id'] ?? null;
        $description = $data['description'] ?? '';
        $priority = $data['priority'] ?? 'medium';
        $preferredContact = $data['preferred_contact'] ?? 'email';
        
        // Validate required fields
        $errors = [];
        if (empty($title)) {
            $errors['title'] = 'Title is required';
        }
        if (empty($serviceId)) {
            $errors['service_id'] = 'Service type is required';
        }
        if (empty($description)) {
            $errors['description'] = 'Description is required';
        }
        
        if (!empty($errors)) {
            // Store form data and errors in session for redisplay
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            $this->flash->addMessage('error', 'Please fix the errors in the form.');
            return $response->withHeader('Location', '/user/new-service-request')->withStatus(302);
        }
        
        // Create the service request
        $serviceRequestModel = new ServiceRequest($this->db);
        
        $requestData = [
            'user_id' => $userId,
            'service_id' => $serviceId,
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'preferred_contact' => $preferredContact,
            'status' => 'pending'
        ];
        
        try {
            $serviceRequestId = $serviceRequestModel->create($requestData);
            
            if ($serviceRequestId) {
                $this->flash->addMessage('success', 'Service request created successfully.');
                unset($_SESSION['form_data']);
                unset($_SESSION['form_errors']);
                return $response->withHeader('Location', '/user/service-request/' . $serviceRequestId)->withStatus(302);
            } else {
                throw new \Exception("Error creating service request");
            }
            
        } catch (\Exception $e) {
            // Log the error
            Logger::error('Error creating service request: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while creating your service request. Please try again later.');
            
            // Store form data in session for redisplay
            $_SESSION['form_data'] = $data;
            return $response->withHeader('Location', '/user/new-service-request')->withStatus(302);
        }
    }
    
    /**
     * Cancel a service request
     */
    public function cancelServiceRequest(Request $request, Response $response, array $args): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!CsrfProtection::verifyToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid form submission, please try again.');
            return $response->withHeader('Location', '/user/service-requests')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $serviceRequestId = (int)$args['id'];
        
        $serviceRequestModel = new ServiceRequest($this->db);
        $serviceRequest = $serviceRequestModel->getById($serviceRequestId);
        
        // Check if service request exists and belongs to current user
        if (!$serviceRequest || $serviceRequest['user_id'] != $userId) {
            $this->flash->addMessage('error', 'Service request not found or access denied.');
            return $response->withHeader('Location', '/user/service-requests')->withStatus(302);
        }
        
        // Can only cancel if it's still in pending status
        if ($serviceRequest['status'] !== 'pending') {
            $this->flash->addMessage('error', 'This service request cannot be cancelled as it is already in progress or completed.');
            return $response->withHeader('Location', '/user/service-request/' . $serviceRequestId)->withStatus(302);
        }
        
        // Cancel the request
        try {
            $result = $serviceRequestModel->updateStatus($serviceRequestId, 'cancelled');
            
            if ($result) {
                $this->flash->addMessage('success', 'Service request cancelled successfully.');
            } else {
                $this->flash->addMessage('error', 'Failed to cancel the service request. Please try again.');
            }
        } catch (\Exception $e) {
            // Log the error
            Logger::error('Error cancelling service request: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while cancelling your service request. Please try again later.');
        }
        
        return $response->withHeader('Location', '/user/service-requests')->withStatus(302);
    }
    
    /**
     * Service History Section
     * Methods to handle user service history
     */
    
    /**
     * Display service history for the logged-in user
     */
    public function serviceHistory(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $serviceHistoryModel = new ServiceHistory($this->db);
        $serviceHistory = $serviceHistoryModel->getAllByUserId($userId);
        
        // Generate CSRF token for feedback forms
        $csrfToken = CsrfProtection::generateToken();
        
        // Render template with service history
        return $this->renderer->render($response, 'user/service-history.php', [
            'title' => 'Service History',
            'serviceHistory' => $serviceHistory,
            'csrfToken' => $csrfToken,
        ]);
    }
    
    /**
     * Add feedback to a completed service
     */
    public function addServiceFeedback(Request $request, Response $response, array $args): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!CsrfProtection::verifyToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid form submission, please try again.');
            return $response->withHeader('Location', '/user/service-history')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $serviceHistoryId = (int)$args['id'];
        
        $serviceHistoryModel = new ServiceHistory($this->db);
        $serviceHistory = $serviceHistoryModel->getById($serviceHistoryId);
        
        // Check if service history exists and belongs to current user
        if (!$serviceHistory || $serviceHistory['user_id'] != $userId) {
            $this->flash->addMessage('error', 'Service history record not found or access denied.');
            return $response->withHeader('Location', '/user/service-history')->withStatus(302);
        }
        
        // Update the feedback
        $rating = min(5, max(1, (int)($data['rating'] ?? 0)));
        $feedback = $data['feedback'] ?? '';
        
        try {
            $result = $serviceHistoryModel->addFeedback($serviceHistoryId, $rating, $feedback);
            
            if ($result) {
                $this->flash->addMessage('success', 'Thank you for your feedback!');
            } else {
                $this->flash->addMessage('error', 'Failed to save feedback. Please try again.');
            }
        } catch (\Exception $e) {
            // Log the error
            Logger::error('Error adding feedback: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while saving your feedback. Please try again later.');
        }
        
        return $response->withHeader('Location', '/user/service-history')->withStatus(302);
    }
    
    /**
     * Consultations Section
     * Methods to handle user consultations
     */
    
    /**
     * Display consultations for the logged-in user
     */
    public function consultations(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $consultationModel = new Consultation($this->db);
        
        // Get upcoming, past, and cancelled consultations
        $upcomingConsultations = $consultationModel->getUpcomingByUserId($userId);
        $pastConsultations = $consultationModel->getPastByUserId($userId);
        $cancelledConsultations = $consultationModel->getCancelledByUserId($userId);
        
        // Generate CSRF token for cancel actions
        $csrfToken = CsrfProtection::generateToken();
        
        // Render template with consultations
        return $this->renderer->render($response, 'user/consultations.php', [
            'title' => 'My Consultations',
            'upcomingConsultations' => $upcomingConsultations,
            'pastConsultations' => $pastConsultations,
            'cancelledConsultations' => $cancelledConsultations,
            'csrfToken' => $csrfToken,
        ]);
    }
    
    /**
     * Show form for scheduling a new consultation
     */
    public function newConsultationForm(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        
        // Get active service requests for the user to link with consultation
        $serviceRequests = [];
        try {
            $stmt = $this->db->prepare(
                "SELECT id, title FROM service_requests 
                WHERE user_id = :user_id AND status != 'cancelled' AND status != 'completed' 
                ORDER BY created_at DESC"
            );
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $serviceRequests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log the error but continue
            Logger::error('Error fetching service requests: ' . $e->getMessage());
        }
        
        // Generate CSRF token for form
        $csrfToken = CsrfProtection::generateToken();
        
        // Render template with the form
        return $this->renderer->render($response, 'user/new-consultation.php', [
            'title' => 'Schedule Consultation',
            'serviceRequests' => $serviceRequests,
            'csrfToken' => $csrfToken,
            'formData' => $_SESSION['form_data'] ?? [],
            'errors' => $_SESSION['form_errors'] ?? [],
        ]);
    }
    
    /**
     * Create a new consultation
     */
    public function createConsultation(Request $request, Response $response): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!CsrfProtection::verifyToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid form submission, please try again.');
            return $response->withHeader('Location', '/user/new-consultation')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $topic = $data['topic'] ?? '';
        $serviceRequestId = !empty($data['service_request_id']) ? (int)$data['service_request_id'] : null;
        $consultationDate = $data['date'] ?? '';
        $consultationTime = $data['time'] ?? '';
        $format = $data['format'] ?? 'video';
        $description = $data['description'] ?? '';
        
        // Validate required fields
        $errors = [];
        if (empty($topic)) {
            $errors['topic'] = 'Topic is required';
        }
        if (empty($consultationDate)) {
            $errors['date'] = 'Date is required';
        }
        if (empty($consultationTime)) {
            $errors['time'] = 'Time is required';
        }
        if (empty($format)) {
            $errors['format'] = 'Format is required';
        }
        
        // Validate date format and ensure it's in the future
        if (!empty($consultationDate) && !empty($consultationTime)) {
            try {
                $dateTime = new \DateTime($consultationDate . ' ' . $consultationTime);
                $now = new \DateTime();
                
                if ($dateTime <= $now) {
                    $errors['date'] = 'Consultation must be scheduled in the future';
                }
            } catch (\Exception $e) {
                $errors['date'] = 'Invalid date or time format';
            }
        }
        
        if (!empty($errors)) {
            // Store form data and errors in session for redisplay
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            $this->flash->addMessage('error', 'Please fix the errors in the form.');
            return $response->withHeader('Location', '/user/new-consultation')->withStatus(302);
        }
        
        // Create the consultation
        $consultationModel = new Consultation($this->db);
        
        $consultationData = [
            'user_id' => $userId,
            'service_request_id' => $serviceRequestId,
            'topic' => $topic,
            'scheduled_at' => $consultationDate . ' ' . $consultationTime,
            'format' => $format,
            'description' => $description,
            'status' => 'scheduled'
        ];
        
        try {
            $consultationId = $consultationModel->create($consultationData);
            
            if ($consultationId) {
                $this->flash->addMessage('success', 'Consultation scheduled successfully.');
                unset($_SESSION['form_data']);
                unset($_SESSION['form_errors']);
                return $response->withHeader('Location', '/user/consultations')->withStatus(302);
            } else {
                throw new \Exception("Error scheduling consultation");
            }
            
        } catch (\Exception $e) {
            // Log the error
            Logger::error('Error scheduling consultation: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while scheduling your consultation. Please try again later.');
            
            // Store form data in session for redisplay
            $_SESSION['form_data'] = $data;
            return $response->withHeader('Location', '/user/new-consultation')->withStatus(302);
        }
    }
    
    /**
     * Cancel a consultation
     */
    public function cancelConsultation(Request $request, Response $response, array $args): Response
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!CsrfProtection::verifyToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid form submission, please try again.');
            return $response->withHeader('Location', '/user/consultations')->withStatus(302);
        }
        
        $userId = $_SESSION['user']['id'];
        $consultationId = (int)$args['id'];
        
        $consultationModel = new Consultation($this->db);
        $consultation = $consultationModel->getById($consultationId);
        
        // Check if consultation exists and belongs to current user
        if (!$consultation || $consultation['user_id'] != $userId) {
            $this->flash->addMessage('error', 'Consultation not found or access denied.');
            return $response->withHeader('Location', '/user/consultations')->withStatus(302);
        }
        
        // Can only cancel if it's still scheduled and hasn't happened yet
        $now = new \DateTime();
        $scheduledAt = new \DateTime($consultation['scheduled_at']);
        
        if ($consultation['status'] !== 'scheduled' || $scheduledAt <= $now) {
            $this->flash->addMessage('error', 'This consultation cannot be cancelled as it has already occurred or is not in scheduled status.');
            return $response->withHeader('Location', '/user/consultations')->withStatus(302);
        }
        
        // Cancel the consultation
        try {
            $result = $consultationModel->updateStatus($consultationId, 'cancelled');
            
            if ($result) {
                $this->flash->addMessage('success', 'Consultation cancelled successfully.');
            } else {
                $this->flash->addMessage('error', 'Failed to cancel the consultation. Please try again.');
            }
        } catch (\Exception $e) {
            // Log the error
            Logger::error('Error cancelling consultation: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while cancelling your consultation. Please try again later.');
        }
        
        return $response->withHeader('Location', '/user/consultations')->withStatus(302);
    }
}