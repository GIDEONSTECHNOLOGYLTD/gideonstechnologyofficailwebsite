<?php
namespace App\Core;

/**
 * Response Class
 * Handles HTTP responses
 */
class Response
{
    /**
     * Set response status code
     *
     * @param int $code HTTP status code
     * @return Response For method chaining
     */
    public function setStatusCode(int $code)
    {
        http_response_code($code);
        return $this;
    }
    
    /**
     * Redirect to a URL
     *
     * @param string $url URL to redirect to
     * @param int $code HTTP status code
     * @return void
     */
    public function redirect(string $url, int $code = 302)
    {
        header("Location: {$url}", true, $code);
        exit;
    }
    
    /**
     * Send JSON response
     *
     * @param mixed $data Data to send as JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function json($data, int $statusCode = 200)
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->setStatusCode($statusCode);
        
        echo json_encode($data);
        exit;
    }
    
    /**
     * Set response header
     *
     * @param string $name Header name
     * @param string $value Header value
     * @return Response For method chaining
     */
    public function setHeader(string $name, string $value)
    {
        header("{$name}: {$value}");
        return $this;
    }
    
    /**
     * Send a file download response
     *
     * @param string $filePath Path to file
     * @param ?string $fileName File name to send to browser
     * @param ?string $contentType Content type
     * @return void
     */
    public function download(string $filePath, ?string $fileName = null, ?string $contentType = null)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }
        
        $fileName = $fileName ?? basename($filePath);
        $contentType = $contentType ?? mime_content_type($filePath);
        
        $this->setHeader('Content-Type', $contentType);
        $this->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->setHeader('Content-Length', filesize($filePath));
        
        readfile($filePath);
        exit;
    }
    
    /**
     * Output content
     *
     * @param string $content Content to output
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function output(string $content, int $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        echo $content;
    }
    
    /**
     * Send a success response with JSON data
     *
     * @param mixed $data Data to include
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function success($data = null, string $message = 'Success', int $statusCode = 200)
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    /**
     * Send an error response with JSON data
     *
     * @param string $message Error message
     * @param mixed $errors Error details
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function error(string $message = 'Error', $errors = null, int $statusCode = 400)
    {
        $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
    
    /**
     * Set a cookie
     *
     * @param string $name Cookie name
     * @param string $value Cookie value
     * @param array $options Cookie options
     * @return Response For method chaining
     */
    public function setCookie(string $name, string $value, array $options = [])
    {
        $defaults = [
            'expires' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => false,
            'samesite' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        setcookie(
            $name,
            $value,
            [
                'expires' => $options['expires'],
                'path' => $options['path'],
                'domain' => $options['domain'],
                'secure' => $options['secure'],
                'httponly' => $options['httponly'],
                'samesite' => $options['samesite']
            ]
        );
        
        return $this;
    }
    
    /**
     * Delete a cookie
     *
     * @param string $name Cookie name
     * @param array $options Cookie options
     * @return Response For method chaining
     */
    public function deleteCookie(string $name, array $options = [])
    {
        $defaults = [
            'path' => '/',
            'domain' => ''
        ];
        
        $options = array_merge($defaults, $options);
        
        return $this->setCookie($name, '', [
            'expires' => time() - 3600,
            'path' => $options['path'],
            'domain' => $options['domain']
        ]);
    }
    
    /**
     * Set HTTP response status code
     * 
     * @param int $code HTTP status code
     * @return Response
     */
    public function status($code)
    {
        http_response_code($code);
        return $this;
    }
}