<?php

namespace App\Services;

use PDO;

class Auth
{
    protected $pdo;
    protected $user = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->checkSession();
    }

    public function check()
    {
        return isset($_SESSION['user_id']);
    }

    public function user()
    {
        if (!$this->user && $this->check()) {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $this->user = $stmt->fetch(PDO::FETCH_OBJ);
        }
        return $this->user;
    }

    public function attempt($email, $password)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user && password_verify($password, $user->password)) {
            $this->login($user);
            return true;
        }

        return false;
    }

    public function login($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;
        $this->user = $user;
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        $this->user = null;
        session_regenerate_id(true);
    }

    protected function checkSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function requireAuth()
    {
        if (!$this->check()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireGuest()
    {
        if ($this->check()) {
            header('Location: /dashboard');
            exit;
        }
    }
}
