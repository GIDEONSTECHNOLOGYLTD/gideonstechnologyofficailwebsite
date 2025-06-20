<?php

namespace App\Core;

/**
 * JWT (JSON Web Token) Utility Class
 * 
 * Handles JWT token generation and validation for API authentication
 */
class JWT
{
    /**
     * Secret key for signing tokens
     * @var string
     */
    private $secret;
    
    /**
     * Token expiration time in seconds
     * @var int
     */
    private $expiration;
    
    /**
     * Constructor
     * 
     * @param string $secret Secret key for signing tokens
     * @param int $expiration Token expiration time in seconds (default: 1 hour)
     */
    public function __construct(string $secret, int $expiration = 3600)
    {
        $this->secret = $secret;
        $this->expiration = $expiration;
    }
    
    /**
     * Generate a JWT token
     * 
     * @param array $payload Data to encode in the token
     * @return string Generated JWT token
     */
    public function generate(array $payload): string
    {
        // Add issued at and expiration time
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expiration;
        
        // Create JWT parts
        $header = $this->base64UrlEncode(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]));
        
        $payload = $this->base64UrlEncode(json_encode($payload));
        
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $this->secret, true)
        );
        
        // Combine parts to create the token
        return "$header.$payload.$signature";
    }
    
    /**
     * Verify a JWT token
     * 
     * @param string $token JWT token to verify
     * @return array|false Decoded payload or false if invalid
     */
    public function verify(string $token)
    {
        // Split token into parts
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($header, $payload, $signature) = $parts;
        
        // Verify signature
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $this->secret, true)
        );
        
        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }
        
        // Decode payload
        $decodedPayload = json_decode($this->base64UrlDecode($payload), true);
        
        // Check expiration
        if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
            return false;
        }
        
        return $decodedPayload;
    }
    
    /**
     * Encode data to Base64URL format
     * 
     * @param string $data Data to encode
     * @return string Base64URL encoded data
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decode Base64URL data
     * 
     * @param string $data Base64URL encoded data
     * @return string Decoded data
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * Get token from Authorization header
     * 
     * @param string $header Authorization header value
     * @return string|null Token or null if not found
     */
    public static function getTokenFromHeader(?string $header): ?string
    {
        if (empty($header) || !preg_match('/Bearer\s+(\S+)/', $header, $matches)) {
            return null;
        }
        
        return $matches[1];
    }
}
