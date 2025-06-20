<?php

namespace App\Services;

use App\Utilities\Logger;

/**
 * Validation Service
 * 
 * Provides consistent data validation across the application
 */
class ValidationService
{
    /**
     * Validation errors
     * @var array
     */
    private $errors = [];
    
    /**
     * Get validation errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Check if validation has errors
     * 
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
    
    /**
     * Add a validation error
     * 
     * @param string $field Field name
     * @param string $message Error message
     * @return self
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }
    
    /**
     * Clear all validation errors
     * 
     * @return self
     */
    public function clearErrors(): self
    {
        $this->errors = [];
        return $this;
    }
    
    /**
     * Validate required fields
     * 
     * @param array $data Data to validate
     * @param array $fields Required fields
     * @return bool True if all required fields are present
     */
    public function required(array $data, array $fields): bool
    {
        $valid = true;
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->addError($field, ucfirst($field) . ' is required');
                $valid = false;
            }
        }
        
        return $valid;
    }
    
    /**
     * Validate email format
     * 
     * @param string $email Email to validate
     * @param string $field Field name
     * @return bool True if email is valid
     */
    public function email(string $email, string $field = 'email'): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'Invalid email format');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate minimum length
     * 
     * @param string $value Value to validate
     * @param int $length Minimum length
     * @param string $field Field name
     * @return bool True if value meets minimum length
     */
    public function minLength(string $value, int $length, string $field): bool
    {
        if (strlen($value) < $length) {
            $this->addError($field, ucfirst($field) . ' must be at least ' . $length . ' characters');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate maximum length
     * 
     * @param string $value Value to validate
     * @param int $length Maximum length
     * @param string $field Field name
     * @return bool True if value meets maximum length
     */
    public function maxLength(string $value, int $length, string $field): bool
    {
        if (strlen($value) > $length) {
            $this->addError($field, ucfirst($field) . ' must not exceed ' . $length . ' characters');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate numeric value
     * 
     * @param mixed $value Value to validate
     * @param string $field Field name
     * @return bool True if value is numeric
     */
    public function numeric($value, string $field): bool
    {
        if (!is_numeric($value)) {
            $this->addError($field, ucfirst($field) . ' must be a number');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate that a value matches another value
     * 
     * @param mixed $value Value to validate
     * @param mixed $match Value to match against
     * @param string $field Field name
     * @return bool True if values match
     */
    public function matches($value, $match, string $field): bool
    {
        if ($value !== $match) {
            $this->addError($field, ucfirst($field) . ' does not match');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate a date format
     * 
     * @param string $date Date to validate
     * @param string $format Expected format
     * @param string $field Field name
     * @return bool True if date is valid
     */
    public function date(string $date, string $format = 'Y-m-d', string $field): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        
        if (!$d || $d->format($format) !== $date) {
            $this->addError($field, ucfirst($field) . ' must be a valid date in format ' . $format);
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate that a value is in a list of allowed values
     * 
     * @param mixed $value Value to validate
     * @param array $allowed Allowed values
     * @param string $field Field name
     * @return bool True if value is in allowed list
     */
    public function inList($value, array $allowed, string $field): bool
    {
        if (!in_array($value, $allowed)) {
            $this->addError($field, ucfirst($field) . ' must be one of: ' . implode(', ', $allowed));
            return false;
        }
        
        return true;
    }
    
    /**
     * Sanitize input data
     * 
     * @param array $data Data to sanitize
     * @return array Sanitized data
     */
    public function sanitize(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Trim whitespace and strip HTML tags
                $sanitized[$key] = trim(strip_tags($value));
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
}
