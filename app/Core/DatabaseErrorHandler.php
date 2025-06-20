<?php

namespace App\Core;

use PDOException;
use App\Utilities\Logger;

/**
 * DatabaseErrorHandler Class
 * 
 * Provides standardized error handling for database operations across the application.
 * This ensures consistent error handling, logging, and user feedback for database errors.
 */
class DatabaseErrorHandler
{
    /**
     * Handle a database exception
     * 
     * @param \PDOException $e The exception to handle
     * @param string $context The context where the exception occurred (e.g., 'User::login')
     * @param bool $throwException Whether to rethrow the exception after handling
     * @return array Error information
     * @throws \Exception If $throwException is true
     */
    public static function handleException(PDOException $e, string $context, bool $throwException = false): array
    {
        // Log the error with context
        Logger::error("Database error in {$context}: " . $e->getMessage());
        Logger::debug("Stack trace: " . $e->getTraceAsString());
        
        // Create standardized error response
        $error = [
            'code' => $e->getCode(),
            'message' => 'A database error occurred',
            'context' => $context,
            'details' => self::getSafeErrorMessage($e)
        ];
        
        // Optionally rethrow the exception
        if ($throwException) {
            throw new \Exception("Database error in {$context}: " . self::getSafeErrorMessage($e), (int)$e->getCode(), $e);
        }
        
        return $error;
    }
    
    /**
     * Handle a general database error
     * 
     * @param string $message Error message
     * @param string $context The context where the error occurred
     * @param int $code Error code
     * @param bool $throwException Whether to throw an exception after handling
     * @return array Error information
     * @throws \Exception If $throwException is true
     */
    public static function handleError(string $message, string $context, int $code = 500, bool $throwException = false): array
    {
        // Log the error with context
        Logger::error("Database error in {$context}: " . $message);
        
        // Create standardized error response
        $error = [
            'code' => $code,
            'message' => 'A database error occurred',
            'context' => $context,
            'details' => $message
        ];
        
        // Optionally throw an exception
        if ($throwException) {
            throw new \Exception("Database error in {$context}: " . $message, $code);
        }
        
        return $error;
    }
    
    /**
     * Get a safe error message that doesn't expose sensitive information
     * 
     * @param \PDOException $e The exception
     * @return string Safe error message
     */
    private static function getSafeErrorMessage(PDOException $e): string
    {
        $message = $e->getMessage();
        
        // Remove potentially sensitive information
        $message = preg_replace('/SQLSTATE\[\w+\]:\s*/', '', $message);
        $message = preg_replace('/\[\w+\]:\s*/', '', $message);
        
        // Check for common error codes and provide more user-friendly messages
        if (strpos($message, '1045') !== false || strpos($message, 'Access denied') !== false) {
            return 'Database authentication error';
        }
        
        if (strpos($message, '1049') !== false || strpos($message, "Unknown database") !== false) {
            return 'Database not found';
        }
        
        if (strpos($message, '2002') !== false || strpos($message, "Connection refused") !== false) {
            return 'Unable to connect to database server';
        }
        
        if (strpos($message, '1062') !== false || strpos($message, "Duplicate entry") !== false) {
            return 'Record already exists';
        }
        
        if (strpos($message, '1451') !== false || strpos($message, "Cannot delete or update a parent row") !== false) {
            return 'Cannot delete this record because it is referenced by other records';
        }
        
        // For development environment, return more details
        if (getenv('APP_ENV') === 'development') {
            return $message;
        }
        
        // For production, return a generic message
        return 'A database error occurred. Please try again later.';
    }
}
