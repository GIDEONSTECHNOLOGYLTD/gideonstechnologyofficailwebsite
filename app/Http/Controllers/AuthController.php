<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

/**
 * Auth Controller
 * 
 * Handles authentication-related routes like login, registration, password reset
 */
class AuthController extends Controller
{
    /**
     * Display login page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function loginForm(Request $request, Response $response): Response
    {
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }
        
        return $this->render($response, 'auth/login.php', [
            'page' => 'login',
            'appName' => $this->container->get('settings')['app_name'] ?? 'Gideon\'s Technology'
        ]);
    }
    
    /**
     * Process login form
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $remember = isset($data['remember']) && $data['remember'] == '1';
        
        // Validate form inputs
        if (empty($email) || empty($password)) {
            return $this->render($response, 'auth/login.php', [
                'page' => 'login',
                'error' => 'Email and password are required',
                'email' => $email
            ]);
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render($response, 'auth/login.php', [
                'page' => 'login',
                'error' => 'Please enter a valid email address',
                'email' => $email
            ]);
        }
        
        // Check for rate limiting
        if ($this->isRateLimited('login_attempts', 5, 300, $email)) {
            return $this->render($response, 'auth/login.php', [
                'page' => 'login',
                'error' => 'Too many login attempts. Please try again later.',
                'email' => $email
            ]);
        }
        
        // In a real application, you would validate against a database
        // For demo purposes, we'll use predefined credentials
        $validCredentials = false;
        $user = null;
        
        // For demo purposes - in production, you'd fetch from database
        $users = $this->getDemoUsers();
        
        foreach ($users as $demoUser) {
            if ($demoUser['email'] === $email && password_verify($password, $demoUser['password_hash'])) {
                $validCredentials = true;
                $user = $demoUser;
                break;
            }
        }
        
        if (!$validCredentials) {
            // Log failed attempt for rate limiting
            $this->recordLoginAttempt('login_attempts', $email);
            
            return $this->render($response, 'auth/login.php', [
                'page' => 'login',
                'error' => 'Invalid email or password',
                'email' => $email
            ]);
        }
        
        // Clear login attempts on successful login
        $this->clearLoginAttempts('login_attempts', $email);
        
        // Set up user session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = time() + (86400 * 30); // 30 days
            
            // In a real app, you would store this token in the database
            // associated with the user, along with an expiry date
            setcookie('remember_token', $token, $expires, '/', '', false, true);
        }
        
        // Log successful login
        if ($this->container->has('logger')) {
            $this->container->get('logger')->info('User logged in successfully', [
                'user_id' => $user['id'],
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }
        
        // Check if there's a redirect URL stored in the session
        $redirectUrl = '/dashboard';
        if (isset($_SESSION['redirect_after_login'])) {
            $redirectUrl = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        } else {
            // Redirect admin users to admin dashboard
            if ($user['role'] === 'admin') {
                $redirectUrl = '/admin/dashboard';
            }
        }
        
        return $response->withHeader('Location', $redirectUrl)->withStatus(302);
    }
    
    /**
     * Logout user
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request, Response $response): Response
    {
        // Clear session data
        $_SESSION = [];
        
        // Clear remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page with a success message
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'You have been successfully logged out.'
        ];
        
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
    
    /**
     * Display registration page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response): Response
    {
        // If user is already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }
        
        return $this->render($response, 'auth/register.php', [
            'page' => 'register'
        ]);
    }
    
    /**
     * Process registration form
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function registerProcess(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';
        
        // Validate form inputs
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        }
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        // Check if email already exists
        $users = $this->getDemoUsers();
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $errors['email'] = 'Email is already registered';
                break;
            }
        }
        
        if (!empty($errors)) {
            return $this->render($response, 'auth/register.php', [
                'page' => 'register',
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'email' => $email
                ]
            ]);
        }
        
        // In a real application, you would save the user to the database
        // For demo purposes, we'll just redirect to login with a success message
        
        // Log user registration
        if ($this->container->has('logger')) {
            $this->container->get('logger')->info('New user registered', [
                'name' => $name,
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }
        
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Registration successful! You can now log in with your credentials.'
        ];
        
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
    
    /**
     * Display forgot password page
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function forgotPassword(Request $request, Response $response): Response
    {
        return $this->render($response, 'auth/forgot-password.php', [
            'page' => 'forgot_password'
        ]);
    }
    
    /**
     * Process forgot password form
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function forgotPasswordProcess(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';
        
        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render($response, 'auth/forgot-password.php', [
                'page' => 'forgot_password',
                'error' => 'Please enter a valid email address',
                'email' => $email
            ]);
        }
        
        // Check if email exists (in a real app, you would check the database)
        $users = $this->getDemoUsers();
        $userExists = false;
        
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $userExists = true;
                break;
            }
        }
        
        // Even if the user doesn't exist, we don't want to reveal that for security reasons
        // We'll show the same success message
        
        // In a real application, you would generate a reset token, save it to the database,
        // and send a password reset email to the user
        
        // Log password reset request
        if ($this->container->has('logger')) {
            $this->container->get('logger')->info('Password reset requested', [
                'email' => $email,
                'user_exists' => $userExists,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }
        
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'If your email address exists in our database, you will receive a password recovery link shortly.'
        ];
        
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
    
    /**
     * Check if login attempts are rate limited
     *
     * @param string $key
     * @param int $maxAttempts
     * @param int $timeWindow
     * @param string $identifier
     * @return bool
     */
    private function isRateLimited(string $key, int $maxAttempts, int $timeWindow, string $identifier): bool
    {
        $attempts = $_SESSION[$key][$identifier] ?? [];
        $attempts = array_filter($attempts, function($timestamp) use ($timeWindow) {
            return $timestamp > time() - $timeWindow;
        });
        
        return count($attempts) >= $maxAttempts;
    }
    
    /**
     * Record login attempt for rate limiting
     *
     * @param string $key
     * @param string $identifier
     */
    private function recordLoginAttempt(string $key, string $identifier): void
    {
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }
        
        if (!isset($_SESSION[$key][$identifier])) {
            $_SESSION[$key][$identifier] = [];
        }
        
        $_SESSION[$key][$identifier][] = time();
    }
    
    /**
     * Clear login attempts
     *
     * @param string $key
     * @param string $identifier
     */
    private function clearLoginAttempts(string $key, string $identifier): void
    {
        if (isset($_SESSION[$key][$identifier])) {
            unset($_SESSION[$key][$identifier]);
        }
    }
    
    /**
     * Get demo users for testing
     * 
     * In a real application, users would be fetched from a database
     *
     * @return array
     */
    private function getDemoUsers(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                // Password: admin123
                'password_hash' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'admin'
            ],
            [
                'id' => 2,
                'name' => 'Regular User',
                'email' => 'user@example.com',
                // Password: password123
                'password_hash' => '$2y$10$hpv5KT5v2LDyj/fKXEYGkO2VuhYJPr6zUMkMgL3gXQT9Fqak4aRc2',
                'role' => 'user'
            ]
        ];
    }
}