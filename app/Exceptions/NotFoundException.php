<?php

namespace App\Exceptions;

use Exception;

/**
 * Not Found Exception
 * 
 * Used when a requested resource is not found
 */
class NotFoundException extends Exception
{
    /**
     * Constructor
     * 
     * @param string $message Exception message
     * @param int $code Exception code
     * @param Exception|null $previous Previous exception
     */
    public function __construct(string $message = 'Resource not found', int $code = 404, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}