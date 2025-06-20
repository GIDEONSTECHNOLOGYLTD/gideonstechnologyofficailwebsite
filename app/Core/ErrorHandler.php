<?php
namespace App\Core;

use App\Utilities\Logger;

class ErrorHandler {
    /**
     * @var ErrorHandler|null Singleton instance
     */
    private static $instance = null;
    
    /**
     * @var \App\Core\Logger|null Logger instance
     */
    private $logger = null;
    
    /**
     * @var array Configuration
     */
    private $config = [];
    
    /**
     * Private constructor to prevent direct instantiation
     * 
     * @param array $config Configuration array
     * @param \App\Core\Logger|null $logger Logger instance
     */
    private function __construct($config = [], $logger = null) {
        $this->config = $config;
        $this->logger = $logger;
        $this->registerErrorHandlers();
    }
    
    /**
     * Get singleton instance
     * 
     * @param array $config Configuration array
     * @param \App\Core\Logger|null $logger Logger instance
     * @return ErrorHandler
     */
    public static function getInstance($config = [], $logger = null) {
        if (self::$instance === null) {
            self::$instance = new self($config, $logger);
        }
        return self::$instance;
    }

    private function registerErrorHandlers() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        $error = [
            'type' => 'Error',
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'code' => $errno
        ];

        return $this->handleErrorInternal($error);
    }

    public function handleException($exception) {
        $error = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'exception' => $exception
        ];

        $this->handleErrorInternal($error);
    }

    public function handleShutdown() {
        $error = error_get_last();
        if ($error !== null) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    private function handleErrorInternal($error) {
        // Determine if we should show detailed errors
        $debug = getenv('APP_ENV') === 'development';
        
        // Generate a unique error ID for reference
        $error_id = 'err_' . substr(md5(uniqid()), 0, 12);
        $error['id'] = $error_id;
        
        // Add request information to the error
        $error['request_uri'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown';
        $error['request_method'] = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'unknown';
        $error['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'none';
        $error['user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
        
        // Add timestamp
        $error['timestamp'] = date('Y-m-d H:i:s');
        
        // Log the error
        if (class_exists('\App\Utilities\Logger')) {
            $logContext = [
                'error_id' => $error_id,
                'file' => $error['file'],
                'line' => $error['line'],
                'type' => $error['type'],
                'code' => $error['code'],
                'request_uri' => $error['request_uri'],
                'request_method' => $error['request_method'],
                'referer' => $error['referer']
            ];
            
            if (isset($error['exception']) && $error['exception'] instanceof \Throwable) {
                $logContext['trace'] = $error['exception']->getTraceAsString();
                
                // For require/include errors, add more context
                if ($error['code'] === 2 && strpos($error['message'], 'require') !== false) {
                    $matches = [];
                    if (preg_match('/require\((.+)\)/', $error['message'], $matches)) {
                        $logContext['missing_file'] = $matches[1];
                        // Check if the directory exists
                        $dir = dirname($matches[1]);
                        $logContext['directory_exists'] = is_dir($dir) ? 'Yes' : 'No';
                        // List files in the directory if it exists
                        if (is_dir($dir)) {
                            $logContext['directory_contents'] = implode(', ', scandir($dir));
                        }
                    }
                }
                
                \App\Utilities\Logger::error($error['message'], $logContext);
            } else {
                \App\Utilities\Logger::error($error['message'], $logContext);
            }
        }
        
        if ($debug) {
            // In development mode, show detailed error information
            $this->showErrorDetails($error);
        } else {
            // In production mode, log the error and show a generic error page
            $this->logError($error);
            $this->showGenericError($error_id);
        }
    }

    private function showErrorDetails($error) {
        header('Content-Type: text/html; charset=UTF-8');
        
        // Add stack trace if available
        if (isset($error['exception']) && $error['exception'] instanceof \Throwable) {
            $error['trace'] = $error['exception']->getTraceAsString();
        }
        
        // Check if we should use the template
        $templatePath = dirname(dirname(__DIR__)) . '/app/templates/errors/development.php';
        if (file_exists($templatePath)) {
            // Extract error data to make it available in the template
            extract(['error' => $error]);
            
            // Capture the template output
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
            
            echo $output;
            exit;
        }
        
        // Fallback to basic HTML if template doesn't exist
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .error { color: #dc3545; }
                .details { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-top: 10px; }
            </style>
        </head>
        <body>
            <h1>An Error Occurred</h1>
            <div class="error">Error: ' . htmlspecialchars($error['message']) . '</div>
            <div class="details">
                <p>File: ' . htmlspecialchars($error['file']) . '</p>
                <p>Line: ' . htmlspecialchars($error['line']) . '</p>
                <p>Type: ' . htmlspecialchars($error['type']) . '</p>
                <p>Code: ' . htmlspecialchars($error['code']) . '</p>
            </div>
        </body>
        </html>';
        exit;
    }

    private function logError($error) {
        $logDir = dirname(dirname(__DIR__)) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/error.log';
        $message = date('Y-m-d H:i:s') . ' - ' . 
                   $error['type'] . ': ' . 
                   $error['message'] . ' in ' . 
                   $error['file'] . ':' . 
                   $error['line'] . "\n";
        
        error_log($message, 3, $logFile);
    }

    private function showGenericError($error_id = null) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: text/html; charset=UTF-8');
        
        // Generate a unique error ID if one wasn't provided
        if ($error_id === null) {
            $error_id = 'err_' . substr(md5(uniqid()), 0, 12);
        }
        
        // Check if we should use the template
        $baseDir = realpath(dirname(dirname(__DIR__)));
        $templatePath = $baseDir . '/app/templates/errors/generic.php';
        
        // Debug information
        if (getenv('APP_ENV') === 'development') {
            error_log("Looking for generic error template at: {$templatePath}");
            error_log("Template exists: " . (file_exists($templatePath) ? 'Yes' : 'No'));
        }
        
        if (file_exists($templatePath)) {
            // Extract error data to make it available in the template
            extract(['error_id' => $error_id]);
            
            // Capture the template output
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
            
            echo $output;
            exit;
        }
        
        // Fallback to basic HTML if template doesn't exist
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>500 - Internal Server Error</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .error { color: #dc3545; }
            </style>
        </head>
        <body>
            <h1 class="error">500 - Internal Server Error</h1>
            <p>An unexpected error occurred. Please try again later.</p>
            <p>Error reference: ' . $error_id . '</p>
        </body>
        </html>';
        exit;
    }
    
    /**
     * Show a 404 Not Found error page
     * 
     * @param string $message Optional custom message
     */
    public function show404($message = 'The requested page could not be found.') {
        // Log the 404 error with request details
        if ($this->logger) {
            $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown';
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'none';
            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
            
            $this->logger->warning("404 Not Found: {$requestUri}", [
                'referer' => $referer,
                'userAgent' => $userAgent,
                'message' => $message
            ]);
        } else if (class_exists('\App\Utilities\Logger')) {
            \App\Utilities\Logger::warning("404 Not Found: {$_SERVER['REQUEST_URI']}");
        }
        
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: text/html; charset=UTF-8');
        
        // Get the absolute path to the template directory
        $baseDir = realpath(dirname(dirname(__DIR__)));
        $templatePath = $baseDir . '/app/templates/errors/404.php';
        
        // Debug information
        if (getenv('APP_ENV') === 'development') {
            error_log("Looking for 404 template at: {$templatePath}");
            error_log("Template exists: " . (file_exists($templatePath) ? 'Yes' : 'No'));
        }
        
        if (file_exists($templatePath)) {
            // Extract error data to make it available in the template
            extract(['message' => $message, 'requestUri' => $_SERVER['REQUEST_URI']]);
            
            // Capture the template output
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
            
            echo $output;
            exit;
        }
        
        // Fallback to basic HTML if template doesn't exist
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Not Found</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .error { color: #dc3545; }
            </style>
        </head>
        <body>
            <h1 class="error">404 - Not Found</h1>
            <p>' . htmlspecialchars($message) . '</p>
            <p><a href="/">Return to Homepage</a></p>
        </body>
        </html>';
        exit;
    }
}
