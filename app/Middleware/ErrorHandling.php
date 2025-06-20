<?php
namespace App\Middleware;

class ErrorHandling {
    private $debugMode;

    public function __construct() {
        $this->debugMode = defined('DEBUG_MODE') && DEBUG_MODE === true;
    }

    public function handle($request, $next) {
        try {
            return $next($request);
        } catch (\Exception $e) {
            return $this->handleException($e, $request);
        } catch (\Error $e) {
            return $this->handleException(new \Exception($e->getMessage(), 0, $e), $request);
        }
    }

    private function handleException($exception, $request) {
        // Log the error
        $this->logError($exception, $request);

        // Prepare error response
        $response = new \App\Core\Response();
        
        if ($this->debugMode) {
            // Show detailed error in debug mode
            $response->setStatusCode(500);
            $response->setContent([
                'error' => 'Internal Server Error',
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        } else {
            // Show generic error in production
            $response->setStatusCode(500);
            $response->setContent([
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred. Please try again later.'
            ]);
        }

        return $response;
    }

    private function logError($exception, $request) {
        $logMessage = "Error: " . $exception->getMessage() . "\n";
        $logMessage .= "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n";
        $logMessage .= "URL: " . $request->getUri() . "\n";
        $logMessage .= "Method: " . $request->getMethod() . "\n";
        $logMessage .= "Trace: " . $exception->getTraceAsString() . "\n";

        error_log($logMessage);

        // Also send to external error tracking service if configured
        if (defined('ERROR_TRACKING_API_KEY') && ERROR_TRACKING_API_KEY) {
            $this->sendToErrorTracking($exception, $request);
        }
    }

    private function sendToErrorTracking($exception, $request) {
        try {
            $data = [
                'timestamp' => time(),
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'url' => $request->getUri(),
                'method' => $request->getMethod(),
                'trace' => $exception->getTraceAsString(),
                'environment' => $this->debugMode ? 'development' : 'production'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.errortracking.com/v1/errors');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . ERROR_TRACKING_API_KEY,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            // Don't throw error if error tracking fails
            error_log("Error tracking failed: " . $e->getMessage());
        }
    }

    public function registerErrorHandlers() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    public function handleShutdown() {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            $exception = new \ErrorException(
                $error['message'],
                $error['type'],
                0,
                $error['file'],
                $error['line']
            );
            $this->handleException($exception, $_SERVER['REQUEST_URI']);
        }
    }
}
