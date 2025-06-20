<?php

namespace App\Middleware;

use App\Core\CacheManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Cache Middleware
 * 
 * Provides response caching for frequently accessed routes
 */
class CacheMiddleware implements MiddlewareInterface
{
    /**
     * @var CacheManager
     */
    protected $cache;
    
    /**
     * @var array Cache configuration
     */
    protected $config;
    
    /**
     * Constructor
     * 
     * @param array $config Cache configuration
     */
    public function __construct(array $config = [])
    {
        $this->cache = CacheManager::getInstance();
        $this->config = array_merge([
            'enabled' => true,
            'routes' => [
                // Example: '/api/v1/products' => 3600 (1 hour)
            ],
            'default_lifetime' => 3600, // 1 hour
            'excluded_query_params' => ['_token', 'timestamp']
        ], $config);
    }
    
    /**
     * Process the request through middleware
     * 
     * @param Request $request The request
     * @param RequestHandler $handler The request handler
     * @return Response The response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Skip caching if disabled or for non-GET requests
        if (!$this->config['enabled'] || $request->getMethod() !== 'GET') {
            return $handler->handle($request);
        }
        
        // Get the request URI and query params
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // Check if this route should be cached
        $lifetime = $this->getRouteLifetime($path);
        if ($lifetime === null) {
            return $handler->handle($request);
        }
        
        // Generate cache key based on URI and query params
        $cacheKey = $this->generateCacheKey($request);
        
        // Try to get response from cache
        $cachedResponse = $this->cache->get($cacheKey);
        if ($cachedResponse) {
            return $cachedResponse;
        }
        
        // Process the request and cache the response
        $response = $handler->handle($request);
        
        // Only cache successful responses
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->cache->set($cacheKey, $response, $lifetime);
        }
        
        return $response;
    }
    
    /**
     * Get cache lifetime for a route
     * 
     * @param string $path Request path
     * @return int|null Cache lifetime in seconds or null if route should not be cached
     */
    protected function getRouteLifetime(string $path): ?int
    {
        // Check for exact route match
        if (isset($this->config['routes'][$path])) {
            return $this->config['routes'][$path];
        }
        
        // Check for pattern matches
        foreach ($this->config['routes'] as $pattern => $lifetime) {
            // If pattern contains wildcards
            if (strpos($pattern, '*') !== false) {
                $regex = str_replace(['/', '*'], ['\/', '.*'], $pattern);
                if (preg_match('/^' . $regex . '$/', $path)) {
                    return $lifetime;
                }
            }
        }
        
        // No match found
        return null;
    }
    
    /**
     * Generate a cache key for a request
     * 
     * @param Request $request The request
     * @return string Cache key
     */
    protected function generateCacheKey(Request $request): string
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        // Get query params and remove excluded ones
        $params = [];
        parse_str($uri->getQuery(), $params);
        
        foreach ($this->config['excluded_query_params'] as $param) {
            unset($params[$param]);
        }
        
        // Sort params to ensure consistent cache keys
        ksort($params);
        
        // Generate key
        $key = 'response_' . md5($path . '?' . http_build_query($params));
        
        // Add user-specific information if available
        $user = $request->getAttribute('user');
        if ($user && isset($user['id'])) {
            $key .= '_user' . $user['id'];
        }
        
        return $key;
    }
}
