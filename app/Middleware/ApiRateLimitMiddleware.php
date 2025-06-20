<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

/**
 * API Rate Limiting Middleware
 * 
 * Limits the number of API requests per client to prevent abuse
 */
class ApiRateLimitMiddleware
{
    /**
     * Maximum number of requests per minute
     * @var int
     */
    private $maxRequestsPerMinute = 60;
    
    /**
     * Storage directory for rate limiting data
     * @var string
     */
    private $storageDir;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->storageDir = __DIR__ . '/../../storage/rate-limits';
        
        // Ensure storage directory exists
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
    }
    
    /**
     * Middleware invokable class
     *
     * @param Request $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Get client identifier (IP address or API key if available)
        $clientId = $this->getClientIdentifier($request);
        
        // Check rate limit
        if (!$this->checkRateLimit($clientId)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => [
                    'code' => 'rate_limit_exceeded',
                    'message' => 'Rate limit exceeded. Please try again later.'
                ]
            ]));
            
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(429); // Too Many Requests
        }
        
        // Rate limit not exceeded, proceed with the request
        return $handler->handle($request);
    }
    
    /**
     * Get client identifier from request
     *
     * @param Request $request
     * @return string
     */
    private function getClientIdentifier(Request $request): string
    {
        // First try to get API token from Authorization header
        $token = $request->getHeaderLine('Authorization');
        if (!empty($token)) {
            // Remove 'Bearer ' prefix if present
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }
            return 'token_' . md5($token);
        }
        
        // Fall back to IP address
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
        return 'ip_' . md5($ip);
    }
    
    /**
     * Check if client has exceeded rate limit
     *
     * @param string $clientId
     * @return bool True if rate limit not exceeded, false otherwise
     */
    private function checkRateLimit(string $clientId): bool
    {
        $filename = $this->storageDir . '/' . $clientId . '.json';
        $now = time();
        $minute = floor($now / 60);
        
        // Initialize or load existing rate limit data
        if (file_exists($filename)) {
            $data = json_decode(file_get_contents($filename), true);
            
            // Reset counter if we're in a new minute
            if ($data['minute'] < $minute) {
                $data = [
                    'minute' => $minute,
                    'count' => 1
                ];
            } else {
                // Increment counter
                $data['count']++;
            }
        } else {
            // New client
            $data = [
                'minute' => $minute,
                'count' => 1
            ];
        }
        
        // Save updated data
        file_put_contents($filename, json_encode($data));
        
        // Check if rate limit exceeded
        return $data['count'] <= $this->maxRequestsPerMinute;
    }
}
