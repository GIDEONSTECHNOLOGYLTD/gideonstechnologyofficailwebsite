<?php
namespace App\Services;

use App\Models\User;

class SocialAuthService {
    private $config;
    private $user;

    public function __construct() {
        $this->config = require APP_PATH . '/config/services.php';
        $this->user = new User();
    }

    public function redirect($provider) {
        $config = $this->getProviderConfig($provider);
        $params = http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => $config['scope']
        ]);

        header('Location: ' . $config['auth_url'] . '?' . $params);
        exit;
    }

    public function callback($provider) {
        if (!isset($_GET['code'])) {
            throw new \Exception('Authorization code not provided');
        }

        $config = $this->getProviderConfig($provider);
        $token = $this->getAccessToken($provider, $_GET['code']);
        $userData = $this->getUserData($provider, $token);

        return $this->findOrCreateUser($userData, $provider);
    }

    private function getProviderConfig($provider) {
        if (!isset($this->config['oauth'][$provider])) {
            throw new \Exception("Provider '{$provider}' not configured");
        }
        return $this->config['oauth'][$provider];
    }

    private function getAccessToken($provider, $code) {
        $config = $this->getProviderConfig($provider);
        
        $params = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'redirect_uri' => $config['redirect_uri'],
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($config['token_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        
        // Set timeout to prevent hanging
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Check for CURL errors
        if ($response === false) {
            throw new \Exception('Network error: ' . $error);
        }

        if ($httpCode !== 200) {
            // Try to get error details from response
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['error_description']) 
                ? $errorData['error_description'] 
                : (isset($errorData['error']) ? $errorData['error'] : 'Unknown error');
                
            throw new \Exception('Failed to get access token: ' . $errorMessage . ' (HTTP Code: ' . $httpCode . ')');
        }

        $data = json_decode($response, true);
        
        // Check if JSON parsing failed
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid response format: ' . json_last_error_msg());
        }
        
        // Check if access token exists in the response
        if (!isset($data['access_token'])) {
            throw new \Exception('Access token not found in the response');
        }
        
        return $data['access_token'];
    }

    private function getUserData($provider, $token) {
        $config = $this->getProviderConfig($provider);
        
        $ch = curl_init($config['user_info_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('Failed to get user data');
        }

        return json_decode($response, true);
    }

    private function findOrCreateUser($userData, $provider) {
        $email = $userData['email'];
        $user = $this->user->getUserByEmail($email);

        if (!$user) {
            $data = [
                'email' => $email,
                'full_name' => $userData['name'] ?? '',
                'oauth_provider' => $provider,
                'oauth_id' => $userData['id']
            ];
            
            $userId = $this->user->create($data);
            return $this->user->getUserById($userId);
        }

        return $user;
    }
}