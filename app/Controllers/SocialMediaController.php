<?php
namespace App\Controllers;

use App\Services\SocialAuthService;

class SocialMediaController extends BaseController {
    private $socialAuth;

    public function __construct() {
        parent::__construct();
        $this->socialAuth = new SocialAuthService();
    }

    public function login($provider) {
        return $this->socialAuth->redirect($provider);
    }

    public function callback($provider) {
        try {
            $user = $this->socialAuth->callback($provider);
            $_SESSION['user_id'] = $user['id'];
            $this->redirect('/dashboard');
        } catch (\Exception $e) {
            $this->view('auth/login', ['error' => $e->getMessage()]);
        }
    }

    public function facebook() {
        return 'Facebook Login';
    }

    public function google() {
        return 'Google Login';
    }
}
