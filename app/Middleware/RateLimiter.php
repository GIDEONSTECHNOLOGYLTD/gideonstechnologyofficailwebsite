<?php
namespace App\Middleware;

class RateLimiter {
    private $redis;
    private $config;

    public function __construct($redis, $config) {
        $this->redis = $redis;
        $this->config = $config;
    }

    public function handle($request, $response, $next) {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $path = $request->getUri()->getPath();
        
        // Skip rate limiting for static assets
        if (preg_match('/\.(js|css|jpg|jpeg|png|gif)$/', $path)) {
            return $next($request, $response);
        }

        // Get rate limit configuration
        $limits = $this->config['rate_limit'] ?? [
            'default' => ['limit' => 100, 'window' => 60], // 100 requests per minute
            'auth' => ['limit' => 20, 'window' => 60], // 20 requests per minute for auth
            'api' => ['limit' => 1000, 'window' => 3600] // 1000 requests per hour for API
        ];

        // Determine which rate limit to use
        $limitKey = 'default';
        if (preg_match('/^(\/api\/|\/auth\/)/', $path)) {
            $limitKey = 'api';
        }

        $limit = $limits[$limitKey]['limit'] ?? $limits['default']['limit'];
        $window = $limits[$limitKey]['window'] ?? $limits['default']['window'];

        // Create unique key for this IP and path
        $key = "rate_limit:{$ip}:{$path}";
        
        // Increment the counter
        $current = $this->redis->incr($key);
        
        // Set expiration if this is the first request in this window
        if ($current === 1) {
            $this->redis->expire($key, $window);
        }

        // Check if we've exceeded the limit
        if ($current > $limit) {
            $resetTime = $this->redis->ttl($key);
            $resetTime = $resetTime > 0 ? $resetTime : 1;

            return $response->withStatus(429)
                ->withHeader('Retry-After', (string)$resetTime)
                ->withJson([
                    'error' => 'Too Many Requests',
                    'message' => 'Please wait before making additional requests',
                    'retry_after' => $resetTime
                ]);
        }

        // Add rate limit headers
        $response = $response->withHeader('X-RateLimit-Limit', (string)$limit)
            ->withHeader('X-RateLimit-Remaining', (string)($limit - $current))
            ->withHeader('X-RateLimit-Reset', (string)time() + $resetTime);

        return $next($request, $response);
    }
}
