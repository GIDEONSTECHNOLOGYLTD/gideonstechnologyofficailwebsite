<?php

namespace App\Exceptions;

/**
 * Custom Exception classes for better error handling
 */

/**
 * Base Exception for the application
 */
class AppException extends \Exception
{
    protected $context = [];
    
    /**
     * Constructor
     *
     * @param string $message Error message
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     * @param array $context Additional context
     */
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }
    
    /**
     * Get context data
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}

/**
 * Exception for not found resources
 */
class NotFoundException extends AppException
{
    public function __construct(string $message = "Resource not found", int $code = 404, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Exception for unauthorized access
 */
class UnauthorizedException extends AppException
{
    public function __construct(string $message = "Unauthorized", int $code = 401, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Exception for forbidden access
 */
class ForbiddenException extends AppException
{
    public function __construct(string $message = "Forbidden", int $code = 403, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Exception for validation errors
 */
class ValidationException extends AppException
{
    protected $errors = [];
    
    /**
     * Constructor
     *
     * @param string $message Error message
     * @param array $errors Validation errors
     * @param int $code Error code
     * @param \Throwable|null $previous Previous exception
     * @param array $context Additional context
     */
    public function __construct(string $message = "Validation failed", array $errors = [], int $code = 422, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
        $this->errors = $errors;
    }
    
    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

/**
 * Exception for database errors
 */
class DatabaseException extends AppException
{
    public function __construct(string $message = "Database error", int $code = 500, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Exception for configuration errors
 */
class ConfigurationException extends AppException
{
    public function __construct(string $message = "Configuration error", int $code = 500, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Exception for service unavailable
 */
class ServiceUnavailableException extends AppException
{
    public function __construct(string $message = "Service unavailable", int $code = 503, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous, $context);
    }
}