<?php
class ErrorHandler {
    private static $instance = null;
    private $logFile;
    private $errorTypes = [
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice'
    ];

    private function __construct() {
        $this->logFile = dirname(__DIR__) . '/logs/error.log';
        $this->setupHandlers();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function setupHandlers() {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleFatalError']);
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        $errorType = $this->errorTypes[$errno] ?? 'Unknown Error';
        $message = sprintf(
            "[%s] %s: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $errorType,
            $errstr,
            $errfile,
            $errline
        );

        $this->logError($message);

        if (error_reporting() & $errno) {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }

        return true;
    }

    public function handleException($exception) {
        $message = sprintf(
            "[%s] Exception: %s in %s on line %d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        $this->logError($message);

        if (php_sapi_name() === 'cli') {
            echo $message . PHP_EOL;
        } else {
            $this->displayError($exception);
        }
    }

    public function handleFatalError() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    private function logError($message) {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        error_log($message . PHP_EOL, 3, $this->logFile);
    }

    private function displayError($exception) {
        if (headers_sent()) {
            echo $this->getErrorTemplate($exception);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => true,
                    'message' => $exception->getMessage()
                ]);
            } else {
                echo $this->getErrorTemplate($exception);
            }
        }
    }

    private function getErrorTemplate($exception) {
        $message = htmlspecialchars($exception->getMessage());
        $trace = DEBUG ? nl2br(htmlspecialchars($exception->getTraceAsString())) : '';
        
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .error { background: #f8d7da; padding: 20px; border-radius: 5px; }
                .trace { margin-top: 20px; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class="error">
                <h2>An error occurred</h2>
                <p>{$message}</p>
                <div class="trace">{$trace}</div>
            </div>
        </body>
        </html>
        HTML;
    }
}