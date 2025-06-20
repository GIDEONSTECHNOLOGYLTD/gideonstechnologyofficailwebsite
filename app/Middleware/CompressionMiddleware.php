<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Compression Middleware
 * 
 * Compresses response content using gzip or deflate
 */
class CompressionMiddleware implements MiddlewareInterface
{
    /**
     * @var array Compression configuration
     */
    protected $config;
    
    /**
     * Constructor
     * 
     * @param array $config Compression configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'enabled' => true,
            'min_length' => 1024, // Minimum content length to compress
            'level' => 6, // Compression level (1-9)
            'excluded_paths' => [],
            'excluded_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'zip', 'gz', 'pdf']
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
        // Process the request first
        $response = $handler->handle($request);
        
        // Skip compression if disabled
        if (!$this->config['enabled']) {
            return $response;
        }
        
        // Check if path is excluded
        $path = $request->getUri()->getPath();
        foreach ($this->config['excluded_paths'] as $excludedPath) {
            if (strpos($path, $excludedPath) === 0) {
                return $response;
            }
        }
        
        // Check if extension is excluded
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $this->config['excluded_extensions'])) {
            return $response;
        }
        
        // Check if response is already compressed
        if ($response->hasHeader('Content-Encoding')) {
            return $response;
        }
        
        // Get the response body
        $body = (string) $response->getBody();
        
        // Skip compression for small responses
        if (strlen($body) < $this->config['min_length']) {
            return $response;
        }
        
        // Check if client accepts compression
        $acceptEncoding = $request->getHeaderLine('Accept-Encoding');
        
        // Determine compression method
        $compressionMethod = null;
        $compressedBody = null;
        
        if (strpos($acceptEncoding, 'gzip') !== false) {
            $compressionMethod = 'gzip';
            $compressedBody = gzencode($body, $this->config['level']);
        } elseif (strpos($acceptEncoding, 'deflate') !== false) {
            $compressionMethod = 'deflate';
            $compressedBody = gzdeflate($body, $this->config['level']);
        }
        
        // If compression was applied, update the response
        if ($compressionMethod && $compressedBody) {
            // Create new response body
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $compressedBody);
            rewind($stream);
            
            // Update response with compressed body and appropriate headers
            $response = $response
                ->withHeader('Content-Encoding', $compressionMethod)
                ->withHeader('Content-Length', strlen($compressedBody))
                ->withHeader('Vary', 'Accept-Encoding')
                ->withBody(new \Slim\Psr7\Stream($stream));
        }
        
        return $response;
    }
}
