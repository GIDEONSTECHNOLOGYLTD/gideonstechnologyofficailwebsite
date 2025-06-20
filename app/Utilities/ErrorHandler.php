<?php

namespace App\Utilities;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpForbiddenException;
use Slim\Views\PhpRenderer;
use Throwable;

/**
 * Global Error Handler
 * 
 * Provides consistent error handling across the application
 */
class ErrorHandler
{
    /**
     * @var PhpRenderer
     */
    private $renderer;
    
    /**
     * @var bool
     */
    private $displayErrorDetails;
    
    /**
     * Constructor
     * 
     * @param PhpRenderer $renderer
     * @param bool $displayErrorDetails Whether to display error details
     */
    public function __construct(PhpRenderer $renderer, bool $displayErrorDetails = false)
    {
        $this->renderer = $renderer;
        $this->displayErrorDetails = $displayErrorDetails;
    }
    
    /**
     * Invoke the error handler
     * 
     * @param Request $request
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     * @return Response
     */
    public function __invoke(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): Response {
        // Log the error
        Logger::error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Determine if this is an API request
        $isApiRequest = $this->isApiRequest($request);
        
        // Create response
        $response = new \Slim\Psr7\Response();
        
        // Get status code
        $statusCode = $this->getStatusCode($exception);
        
        // Handle API errors
        if ($isApiRequest) {
            return $this->handleApiError($response, $exception, $statusCode);
        }
        
        // Handle web errors
        return $this->handleWebError($response, $exception, $statusCode);
    }
    
    /**
     * Check if this is an API request
     * 
     * @param Request $request
     * @return bool
     */
    private function isApiRequest(Request $request): bool
    {
        $path = $request->getUri()->getPath();
        
        // Check if path starts with /api
        if (strpos($path, '/api') === 0) {
            return true;
        }
        
        // Check if request wants JSON
        $acceptHeader = $request->getHeaderLine('Accept');
        if (strpos($acceptHeader, 'application/json') !== false) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get appropriate status code for exception
     * 
     * @param Throwable $exception
     * @return int
     */
    private function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof HttpNotFoundException) {
            return 404;
        }
        
        if ($exception instanceof HttpMethodNotAllowedException) {
            return 405;
        }
        
        if ($exception instanceof HttpUnauthorizedException) {
            return 401;
        }
        
        if ($exception instanceof HttpForbiddenException) {
            return 403;
        }
        
        // Default to 500 for all other exceptions
        return 500;
    }
    
    /**
     * Handle API error
     * 
     * @param Response $response
     * @param Throwable $exception
     * @param int $statusCode
     * @return Response
     */
    private function handleApiError(Response $response, Throwable $exception, int $statusCode): Response
    {
        $error = [
            'success' => false,
            'error' => [
                'code' => $this->getErrorCode($exception, $statusCode),
                'message' => $this->getErrorMessage($exception, $statusCode)
            ]
        ];
        
        // Add debug information if enabled
        if ($this->displayErrorDetails) {
            $error['error']['debug'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ];
        }
        
        $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
    
    /**
     * Handle web error
     * 
     * @param Response $response
     * @param Throwable $exception
     * @param int $statusCode
     * @return Response
     */
    private function handleWebError(Response $response, Throwable $exception, int $statusCode): Response
    {
        $data = [
            'title' => $this->getErrorTitle($statusCode),
            'message' => $this->getErrorMessage($exception, $statusCode)
        ];
        
        // Add debug information if enabled
        if ($this->displayErrorDetails) {
            $data['debug'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ];
        }
        
        // Render appropriate error template
        try {
            $template = "errors/{$statusCode}.php";
            return $this->renderer->render($response, $template, $data)->withStatus($statusCode);
        } catch (\Throwable $e) {
            // If specific error template not found, use generic error template
            try {
                return $this->renderer->render($response, 'errors/error.php', $data)->withStatus($statusCode);
            } catch (\Throwable $e) {
                // If all else fails, return plain text error
                $response->getBody()->write("Error {$statusCode}: {$data['message']}");
                return $response->withStatus($statusCode);
            }
        }
    }
    
    /**
     * Get error code for API responses
     * 
     * @param Throwable $exception
     * @param int $statusCode
     * @return string
     */
    private function getErrorCode(Throwable $exception, int $statusCode): string
    {
        $errorCodes = [
            400 => 'bad_request',
            401 => 'unauthorized',
            403 => 'forbidden',
            404 => 'not_found',
            405 => 'method_not_allowed',
            422 => 'validation_error',
            429 => 'too_many_requests',
            500 => 'server_error'
        ];
        
        return $errorCodes[$statusCode] ?? 'error';
    }
    
    /**
     * Get error title for web responses
     * 
     * @param int $statusCode
     * @return string
     */
    private function getErrorTitle(int $statusCode): string
    {
        $titles = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Page Not Found',
            405 => 'Method Not Allowed',
            422 => 'Validation Error',
            429 => 'Too Many Requests',
            500 => 'Server Error'
        ];
        
        return $titles[$statusCode] ?? 'Error';
    }
    
    /**
     * Get user-friendly error message
     * 
     * @param Throwable $exception
     * @param int $statusCode
     * @return string
     */
    private function getErrorMessage(Throwable $exception, int $statusCode): string
    {
        // Use exception message if display error details is enabled
        if ($this->displayErrorDetails) {
            return $exception->getMessage();
        }
        
        // Otherwise use generic message based on status code
        $messages = [
            400 => 'The request could not be understood by the server.',
            401 => 'Authentication is required to access this resource.',
            403 => 'You do not have permission to access this resource.',
            404 => 'The page you are looking for could not be found.',
            405 => 'The method specified in the request is not allowed.',
            422 => 'The submitted data is invalid.',
            429 => 'You have made too many requests. Please try again later.',
            500 => 'An unexpected error occurred. Please try again later.'
        ];
        
        return $messages[$statusCode] ?? 'An error occurred. Please try again later.';
    }
}
