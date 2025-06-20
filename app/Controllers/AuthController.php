<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utilities\Logger;
use App\Core\FileRateLimiter;
use App\Core\CsrfProtection;
use App\Core\TwoFactorAuth;

/**
 * Authentication Controller
 * 
 * Handles user authentication, registration, and password management
 */
class AuthController extends BaseController
{
    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }
    
    /**
     * Display login form
     */
    public function loginForm(Request $request, Response $response): Response
    {
        return $this->render($response, 'auth/login.php', [
            'title' => 'Login',
            'redirect' => $_SESSION['redirect_after_login'] ?? null
        ]);
    }
    
    /**
     * Process login
     */
    public function login(Request $request, Response $response): Response
    {
        // Validate CSRF token
        $data = $request->getParsedBody();
        $token = $data['csrf_token'] ?? '';
        if (!CsrfProtection::validateToken($token)) {
            $this->flash->addMessage('error', 'Invalid security token. Please try again.');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $remember = isset($data['remember']) && $data['remember'] === 'on';
        
        // Check for rate limiting
        $rateLimiter = new FileRateLimiter();
        $key = 'login:' . strtolower($email);
        
        if ($rateLimiter->tooManyAttempts($key, 5, 15)) {
            $seconds = $rateLimiter->availableIn($key);
            $minutes = ceil($seconds / 60);
            $this->flash->addMessage('error', 
                'Too many login attempts. Please try again in ' . 
                ($minutes > 1 ? $minutes . ' minutes' : '1 minute') . '.');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Basic validation
        if (empty($email) || empty($password)) {
            // Increment the rate limiter counter for invalid attempts
            $rateLimiter->increment($key);
            $this->flash->addMessage('error', 'Email and password are required');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Get user from database
        try {
            $db = $this->container->get('db');
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Verify user exists and password is correct
            if (!$user || !password_verify($password, $user['password'])) {
                // Increment the rate limiter counter for invalid attempts
                $rateLimiter->increment($key);
                $this->flash->addMessage('error', 'Invalid email or password');
                return $response->withHeader('Location', '/auth/login')->withStatus(302);
            }
            
            // Reset rate limiter on successful login
            $rateLimiter->reset($key);
            
            // Check if user is active
            if (isset($user['status']) && $user['status'] !== 'active') {
                $this->flash->addMessage('error', 'Your account is inactive. Please contact support.');
                return $response->withHeader('Location', '/auth/login')->withStatus(302);
            }
            
            // Check if two-factor authentication is enabled
            if (isset($user['two_factor_enabled']) && $user['two_factor_enabled']) {
                // Store user ID in session for 2FA verification
                $_SESSION['2fa_user_id'] = $user['id'];
                
                // If remember me was checked, store it in session for after 2FA
                if ($remember) {
                    $_SESSION['2fa_remember'] = true;
                }
                
                Logger::info('User login awaiting 2FA verification: ' . $user['email']);
                
                // Redirect to 2FA verification page
                return $response->withHeader('Location', '/auth/verify-2fa')->withStatus(302);
            }
            
            // If 2FA is not enabled, proceed with normal login
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'] ?? '',
                'role' => $user['role'] ?? 'user',
            ];
            
            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + 60 * 60 * 24 * 30; // 30 days
                
                // Store token in database
                $stmt = $db->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();
                
                // Set cookie
                setcookie('remember_token', $token, $expires, '/', '', true, true);
            }
            
            Logger::info('User logged in: ' . $user['email']);
            
            // Redirect based on user role
            if (isset($user['role']) && $user['role'] === 'admin') {
                $redirect = $_SESSION['redirect_after_login'] ?? '/admin';
            } else {
                $redirect = $_SESSION['redirect_after_login'] ?? '/user/dashboard';
            }
            unset($_SESSION['redirect_after_login']);
            
            return $response->withHeader('Location', $redirect)->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Login error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred during login');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
    }
    
    /**
     * Display registration form
     */
    public function registerForm(Request $request, Response $response): Response
    {
        return $this->render($response, 'auth/register.php', [
            'title' => 'Create Account',
            'appName' => 'Gideons Technology'
        ]);
    }
    
    /**
     * Process registration
     */
    public function register(Request $request, Response $response): Response
    {
        // Validate CSRF token
        $data = $request->getParsedBody();
        $token = $data['csrf_token'] ?? '';
        if (!CsrfProtection::validateToken($token)) {
            $this->flash->addMessage('error', 'Invalid security token. Please try again.');
            return $response->withHeader('Location', '/auth/register')->withStatus(302);
        }
        
        // Check for rate limiting on registration attempts
        $rateLimiter = new FileRateLimiter();
        $key = 'register:' . $_SERVER['REMOTE_ADDR'];
        
        if ($rateLimiter->tooManyAttempts($key, 3, 60)) { // Stricter limits for registration
            $seconds = $rateLimiter->availableIn($key);
            $minutes = ceil($seconds / 60);
            $this->flash->addMessage('error', 
                'Too many registration attempts. Please try again in ' . 
                ($minutes > 1 ? $minutes . ' minutes' : '1 minute') . '.');
            return $response->withHeader('Location', '/auth/register')->withStatus(302);
        }
        
        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $username = $data['username'] ?? '';
        $phone = $data['phone'] ?? null;
        $password = $data['password'] ?? '';
        $confirmPassword = $data['password_confirm'] ?? '';
        
        // Basic validation
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            // Store errors in flash and redirect back with input data
            $this->flash->addMessage('errors', json_encode($errors));
            $this->flash->addMessage('old', json_encode($data));
            return $response->withHeader('Location', '/auth/register')->withStatus(302);
        }
        
        try {
            $db = $this->container->get('db');
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                $this->flash->addMessage('error', 'Email already registered');
                $this->flash->addMessage('old', json_encode($data));
                return $response->withHeader('Location', '/auth/register')->withStatus(302);
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $db->prepare("
                INSERT INTO users (name, email, username, phone, password, role, status, created_at) 
                VALUES (:name, :email, :username, :phone, :password, 'user', 'active', NOW())
            ");
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();
            
            $userId = $db->lastInsertId();
            
            Logger::info('User registered: ' . $email);
            
            // Automatically log the user in
            $_SESSION['user'] = [
                'id' => $userId,
                'email' => $email,
                'name' => $name,
                'role' => 'user',
            ];
            
            $this->flash->addMessage('success', 'Registration successful! Welcome to your account.');
            return $response->withHeader('Location', '/user/dashboard')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Registration error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred during registration');
            $this->flash->addMessage('old', json_encode($data));
            return $response->withHeader('Location', '/auth/register')->withStatus(302);
        }
    }
    
    /**
     * Display forgot password form
     */
    public function forgotForm(Request $request, Response $response): Response
    {
        return $this->render($response, 'auth/forgot-password.php', [
            'title' => 'Forgot Password'
        ]);
    }
    
    /**
     * Process forgot password request
     */
    public function forgot(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash->addMessage('error', 'Please enter a valid email address');
            return $response->withHeader('Location', '/auth/forgot-password')->withStatus(302);
        }
        
        try {
            $db = $this->container->get('db');
            
            // Check if email exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
                
                // Store token in database
                $stmt = $db->prepare("
                    INSERT INTO password_resets (email, token, expires_at, created_at) 
                    VALUES (:email, :token, :expires, NOW())
                    ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires, created_at = NOW()
                ");
                
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':expires', $expires);
                $stmt->execute();
                
                // In a real application, send email with reset link
                // For now, just log it
                Logger::info('Password reset requested for: ' . $email . ' with token: ' . $token);
            }
            
            // Always show success to prevent email enumeration
            $this->flash->addMessage('success', 'If your email exists in our system, you will receive a password reset link shortly.');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Forgot password error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while processing your request');
            return $response->withHeader('Location', '/auth/forgot-password')->withStatus(302);
        }
    }
    
    /**
     * Display reset password form
     */
    public function resetForm(Request $request, Response $response, array $args): Response
    {
        $token = $args['token'] ?? '';
        
        if (empty($token)) {
            $this->flash->addMessage('error', 'Invalid password reset token');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        return $this->render($response, 'auth/reset-password.php', [
            'title' => 'Reset Password',
            'token' => $token
        ]);
    }
    
    /**
     * Process password reset
     */
    public function reset(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $token = $data['token'] ?? '';
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';
        
        if (empty($token)) {
            $this->flash->addMessage('error', 'Invalid password reset token');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        if (empty($password)) {
            $this->flash->addMessage('error', 'Password is required');
            return $response->withHeader('Location', '/auth/reset-password/' . $token)->withStatus(302);
        }
        
        if (strlen($password) < 8) {
            $this->flash->addMessage('error', 'Password must be at least 8 characters');
            return $response->withHeader('Location', '/auth/reset-password/' . $token)->withStatus(302);
        }
        
        if ($password !== $confirmPassword) {
            $this->flash->addMessage('error', 'Passwords do not match');
            return $response->withHeader('Location', '/auth/reset-password/' . $token)->withStatus(302);
        }
        
        try {
            $db = $this->container->get('db');
            
            // Verify token exists and is not expired
            $stmt = $db->prepare("
                SELECT email FROM password_resets 
                WHERE token = :token AND expires_at > NOW() 
                LIMIT 1
            ");
            
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            $reset = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$reset) {
                $this->flash->addMessage('error', 'Invalid or expired password reset token');
                return $response->withHeader('Location', '/auth/login')->withStatus(302);
            }
            
            $email = $reset['email'];
            
            // Update user password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Delete used token
            $stmt = $db->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            Logger::info('Password reset successful for: ' . $email);
            
            $this->flash->addMessage('success', 'Your password has been reset successfully. You can now log in with your new password.');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('Password reset error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred while resetting your password');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
    }
    
    /**
     * Display two-factor authentication verification form
     */
    public function verifyTwoFactorForm(Request $request, Response $response): Response
    {
        // Check if 2FA verification is in progress
        if (!isset($_SESSION['2fa_user_id'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        // Use the tfa_verification.php file instead of verify_2fa.php due to gitignore restrictions
        return $this->render($response, 'auth/tfa_verification.php', [
            'title' => 'Verify Two-Factor Authentication'
        ]);
    }

    /**
     * Verify two-factor authentication code
     */
    public function verifyTwoFactor(Request $request, Response $response): Response
    {
        // Check if 2FA verification is in progress
        if (!isset($_SESSION['2fa_user_id'])) {
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
        
        $userId = $_SESSION['2fa_user_id'];
        $data = $request->getParsedBody();
        $code = $data['code'] ?? '';
        $recoveryCode = $data['recovery_code'] ?? '';
        
        // Validate CSRF token
        if (!CsrfProtection::validateToken($data['csrf_token'] ?? '')) {
            $this->flash->addMessage('error', 'Invalid security token');
            return $response->withHeader('Location', '/auth/verify-2fa')->withStatus(302);
        }
        
        try {
            $db = $this->container->get('db');
            
            // Get user
            $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new \Exception('User not found');
            }
            
            $twoFactorAuth = new TwoFactorAuth();
            $verified = false;
            
            // Check if using a recovery code
            if (!empty($recoveryCode)) {
                try {
                    $stmt = $db->prepare("
                        SELECT * FROM two_factor_recovery_codes 
                        WHERE user_id = :user_id AND code = :code AND used = 0
                        LIMIT 1
                    ");
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->bindParam(':code', $recoveryCode);
                    $stmt->execute();
                    $recoveryCodeRecord = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($recoveryCodeRecord) {
                        // Mark recovery code as used
                        $stmt = $db->prepare("
                            UPDATE two_factor_recovery_codes 
                            SET used = 1 
                            WHERE id = :id
                        ");
                        $stmt->bindParam(':id', $recoveryCodeRecord['id']);
                        $stmt->execute();
                        
                        $verified = true;
                    }
                } catch (\Exception $e) {
                    // Recovery codes table might not exist yet
                    Logger::debug('Recovery codes table not found: ' . $e->getMessage());
                }
            } else if (!empty($code)) {
                // Verify TOTP code
                $verified = $twoFactorAuth->verifyCode($user['two_factor_secret'], $code);
            }
            
            if (!$verified) {
                $this->flash->addMessage('error', 'Invalid verification code. Please try again.');
                return $response->withHeader('Location', '/auth/verify-2fa')->withStatus(302);
            }
            
            // Complete login process
            unset($_SESSION['2fa_user_id']);
            
            // Store user in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'] ?? '',
                'role' => $user['role'] ?? 'user',
            ];
            
            // Set remember me cookie if requested during login
            if (isset($_SESSION['2fa_remember']) && $_SESSION['2fa_remember']) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + 60 * 60 * 24 * 30; // 30 days
                
                // Store token in database
                $stmt = $db->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();
                
                // Set cookie
                setcookie('remember_token', $token, $expires, '/', '', true, true);
                
                // Clean up session
                unset($_SESSION['2fa_remember']);
            }
            
            // Log the activity
            Logger::info('User completed 2FA verification: ' . $user['email']);
            
            // Redirect to requested page or dashboard
            $redirect = $_SESSION['redirect_after_login'] ?? '/user/dashboard';
            unset($_SESSION['redirect_after_login']);
            
            $this->flash->addMessage('success', 'Login successful');
            return $response->withHeader('Location', $redirect)->withStatus(302);
            
        } catch (\Exception $e) {
            Logger::error('2FA verification error: ' . $e->getMessage());
            $this->flash->addMessage('error', 'An error occurred during verification');
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }
    }

    /**
     * Log user out
     */
    public function logout(Request $request, Response $response): Response
    {
        // Log the logout if user was logged in
        if (isset($_SESSION['user'])) {
            Logger::info('User logged out: ' . ($_SESSION['user']['email'] ?? 'Unknown'));
        }
        
        // Clear session
        session_unset();
        session_destroy();
        
        // Clear remember me cookie if it exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
        
        // Redirect to login page
        return $response->withHeader('Location', '/auth/login')->withStatus(302);
    }
}
