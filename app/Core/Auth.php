<?php

namespace App\Core;

use App\Models\User;

/**
 * Auth Class
 * Handles user authentication
 */
class Auth
{
    /**
     * The authenticated user
     *
     * @var User|null
     */
    private static $user = null;

    /**
     * Check if a user is logged in
     *
     * @return bool
     */
    public static function check()
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    /**
     * Get the currently authenticated user
     *
     * @return User|null
     */
    public static function user()
    {
        if (self::$user === null && self::check()) {
            $userModel = new User();
            self::$user = $userModel->findById($_SESSION['user_id']);
        }
        
        return self::$user;
    }

    /**
     * Login a user
     *
     * @param User $user
     * @return bool
     */
    public static function login(User $user)
    {
        $_SESSION['user_id'] = $user->id;
        self::$user = $user;
        return true;
    }

    /**
     * Logout the current user
     *
     * @return void
     */
    public static function logout()
    {
        unset($_SESSION['user_id']);
        self::$user = null;
        session_regenerate_id(true);
    }

    /**
     * Check if the current user is an admin
     * 
     * @return bool
     */
    public static function isAdmin() {
        $user = self::user();
        return $user && isset($user['role']) && $user['role'] === 'admin';
    }
}