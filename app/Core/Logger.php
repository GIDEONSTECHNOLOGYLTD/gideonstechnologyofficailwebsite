<?php

namespace App\Core;

/**
 * Logger Class
 * Handles application logging with different severity levels
 */
class Logger
{
    private $logPath;
    private $logLevel;
    private $dateFormat;
    
    // Log levels (PSR-3 compliant)
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    
    // Ordered log levels for filtering
    private $levels = [
        self::EMERGENCY => 0,
        self::ALERT     => 1,
        self::CRITICAL  => 2, 
        self::ERROR     => 3,
        self::WARNING   => 4,
        self::NOTICE    => 5,
        self::INFO      => 6,
        self::DEBUG     => 7
    ];
    
    /**
     * Constructor
     *
     * @param string $logPath Path to log files directory
     * @param string $logLevel Minimum log level to record
     * @param string $dateFormat Date format for log entries
     */
    public function __construct($logPath = null, $logLevel = self::DEBUG, $dateFormat = 'Y-m-d H:i:s')
    {
        $this->logPath = $logPath ?? dirname(__DIR__, 2) . '/storage/logs';
        $this->logLevel = $logLevel;
        $this->dateFormat = $dateFormat;
        
        // Create log directory if it doesn't exist
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }
    
    /**
     * Log a message
     *
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function log($level, $message, array $context = [])
    {
        // Check if this level should be logged
        if (!$this->shouldLog($level)) {
            return false;
        }
        
        $logFile = $this->getLogFile();
        $timestamp = date($this->dateFormat);
        $contextString = empty($context) ? '' : ' ' . $this->formatContext($context);
        
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextString}" . PHP_EOL;
        
        return file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) !== false;
    }
    
    /**
     * Get log file path for today
     *
     * @return string Log file path
     */
    private function getLogFile()
    {
        $filename = date('Y-m-d') . '.log';
        return $this->logPath . '/' . $filename;
    }
    
    /**
     * Check if log level meets minimum threshold
     *
     * @param string $level Log level to check
     * @return bool Should be logged
     */
    private function shouldLog($level)
    {
        if (!isset($this->levels[$level])) {
            return false;
        }
        
        return $this->levels[$level] <= $this->levels[$this->logLevel];
    }
    
    /**
     * Format context data for logging
     *
     * @param array $context Context data
     * @return string Formatted context
     */
    private function formatContext(array $context)
    {
        $result = [];
        
        foreach ($context as $key => $value) {
            // Handle objects and array
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES);
            }
            
            $result[] = "{$key}={$value}";
        }
        
        return count($result) ? '[' . implode(', ', $result) . ']' : '';
    }
    
    /**
     * Emergency log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function emergency($message, array $context = [])
    {
        $this->log(self::EMERGENCY, $message, $context);
    }
    
    /**
     * Alert log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function alert($message, array $context = [])
    {
        $this->log(self::ALERT, $message, $context);
    }
    
    /**
     * Critical log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function critical($message, array $context = [])
    {
        $this->log(self::CRITICAL, $message, $context);
    }
    
    /**
     * Error log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function error($message, array $context = [])
    {
        $this->log(self::ERROR, $message, $context);
    }
    
    /**
     * Warning log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function warning($message, array $context = [])
    {
        $this->log(self::WARNING, $message, $context);
    }
    
    /**
     * Notice log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function notice($message, array $context = [])
    {
        $this->log(self::NOTICE, $message, $context);
    }
    
    /**
     * Info log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function info($message, array $context = [])
    {
        $this->log(self::INFO, $message, $context);
    }
    
    /**
     * Debug log
     *
     * @param string $message Log message
     * @param array $context Additional context data
     */
    public function debug($message, array $context = [])
    {
        $this->log(self::DEBUG, $message, $context);
    }
}
