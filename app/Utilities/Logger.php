<?php

namespace App\Utilities;

/**
 * Logger Utility
 * Provides consistent logging across the application
 */
class Logger
{
    // Log levels
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const NOTICE = 'NOTICE';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';
    const ALERT = 'ALERT';
    const EMERGENCY = 'EMERGENCY';
    
    // Log destinations
    const FILE = 'file';
    const DATABASE = 'database';
    const SYSLOG = 'syslog';
    
    private $config;
    private $logPath;
    private static $instance = null;
    
    /**
     * Constructor
     *
     * @param array $config Logger configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->logPath = $config['path'] ?? __DIR__ . '/../../logs';
        
        // Create log directory if it doesn't exist
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }
    
    /**
     * Initialize static logger with configuration
     *
     * @param string $logPath Path to log file
     * @param bool $debug Debug mode
     * @return void
     */
    public static function init($logPath = null, $debug = false)
    {
        $config = [
            'path' => $logPath ?? __DIR__ . '/../../logs',
            'debug' => $debug
        ];
        
        self::$instance = new self($config);
    }
    
    /**
     * Get singleton instance
     *
     * @param array $config Configuration array
     * @return Logger
     */
    public static function getInstance(array $config = [])
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    
    /**
     * Static debug method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function debug($message, array $context = [])
    {
        return self::getInstance()->debugInstance($message, $context);
    }
    
    /**
     * Instance debug method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function debugInstance($message, array $context = [])
    {
        return $this->log(self::DEBUG, $message, $context);
    }
    
    /**
     * Static info method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function info($message, array $context = [])
    {
        return self::getInstance()->infoInstance($message, $context);
    }
    
    /**
     * Instance info method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function infoInstance($message, array $context = [])
    {
        return $this->log(self::INFO, $message, $context);
    }
    
    /**
     * Static notice method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function notice($message, array $context = [])
    {
        return self::getInstance()->noticeInstance($message, $context);
    }
    
    /**
     * Instance notice method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function noticeInstance($message, array $context = [])
    {
        return $this->log(self::NOTICE, $message, $context);
    }
    
    /**
     * Static warning method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function warning($message, array $context = [])
    {
        return self::getInstance()->warningInstance($message, $context);
    }
    
    /**
     * Instance warning method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function warningInstance($message, array $context = [])
    {
        return $this->log(self::WARNING, $message, $context);
    }
    
    /**
     * Static error method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function error($message, array $context = [])
    {
        return self::getInstance()->errorInstance($message, $context);
    }
    
    /**
     * Instance error method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function errorInstance($message, array $context = [])
    {
        return $this->log(self::ERROR, $message, $context);
    }
    
    /**
     * Static critical method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function critical($message, array $context = [])
    {
        return self::getInstance()->criticalInstance($message, $context);
    }
    
    /**
     * Instance critical method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function criticalInstance($message, array $context = [])
    {
        return $this->log(self::CRITICAL, $message, $context);
    }
    
    /**
     * Static alert method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function alert($message, array $context = [])
    {
        return self::getInstance()->alertInstance($message, $context);
    }
    
    /**
     * Instance alert method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function alertInstance($message, array $context = [])
    {
        return $this->log(self::ALERT, $message, $context);
    }
    
    /**
     * Static emergency method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public static function emergency($message, array $context = [])
    {
        return self::getInstance()->emergencyInstance($message, $context);
    }
    
    /**
     * Instance emergency method
     *
     * @param string $message Log message
     * @param array $context Additional context data
     * @return bool Success status
     */
    public function emergencyInstance($message, array $context = [])
    {
        return $this->log(self::EMERGENCY, $message, $context);
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
        $timestamp = new \DateTime();
        $destination = $this->config['destination'] ?? self::FILE;
        
        // Prepare log entry
        $entry = [
            'timestamp' => $timestamp->format('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
        
        // Add request info if available
        if (isset($_SERVER['REQUEST_URI'])) {
            $entry['request_uri'] = $_SERVER['REQUEST_URI'];
            $entry['request_method'] = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
            $entry['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        }
        
        // Route to appropriate logger
        switch ($destination) {
            case self::DATABASE:
                return $this->logToDatabase($entry);
            case self::SYSLOG:
                return $this->logToSyslog($entry);
            case self::FILE:
            default:
                return $this->logToFile($entry);
        }
    }
    
    /**
     * Log to file
     *
     * @param array $entry Log entry
     * @return bool Success status
     */
    private function logToFile(array $entry)
    {
        $logFile = $this->getLogFilePath($entry['level']);
        $line = $this->formatLogLine($entry);
        
        return error_log($line . PHP_EOL, 3, $logFile);
    }
    
    /**
     * Log to database
     *
     * @param array $entry Log entry
     * @return bool Success status
     */
    private function logToDatabase(array $entry)
    {
        // Implement database logging here
        // This would typically insert a record into a logs table
        return true;
    }
    
    /**
     * Log to syslog
     *
     * @param array $entry Log entry
     * @return bool Success status
     */
    private function logToSyslog(array $entry)
    {
        $priority = $this->getSyslogPriority($entry['level']);
        $message = $this->formatLogLine($entry);
        
        return syslog($priority, $message);
    }
    
    /**
     * Get appropriate log file path
     *
     * @param string $level Log level
     * @return string Log file path
     */
    private function getLogFilePath($level)
    {
        $date = date('Y-m-d');
        
        // Group error levels into files
        if (in_array($level, [self::ERROR, self::CRITICAL, self::ALERT, self::EMERGENCY])) {
            return $this->logPath . "/error-{$date}.log";
        } elseif (in_array($level, [self::WARNING, self::NOTICE])) {
            return $this->logPath . "/warning-{$date}.log";
        } else {
            return $this->logPath . "/app-{$date}.log";
        }
    }
    
    /**
     * Format log line for file
     *
     * @param array $entry Log entry
     * @return string Formatted log line
     */
    private function formatLogLine(array $entry)
    {
        $context = '';
        
        if (!empty($entry['context'])) {
            $context = ' ' . json_encode($entry['context']);
        }
        
        $requestInfo = '';
        if (isset($entry['request_uri'])) {
            $requestInfo = " | {$entry['request_method']} {$entry['request_uri']} | {$entry['ip']}";
        }
        
        return "[{$entry['timestamp']}] {$entry['level']}:{$requestInfo} {$entry['message']}{$context}";
    }
    
    /**
     * Get syslog priority based on log level
     *
     * @param string $level Log level
     * @return int Syslog priority
     */
    private function getSyslogPriority($level)
    {
        switch ($level) {
            case self::EMERGENCY:
                return LOG_EMERG;
            case self::ALERT:
                return LOG_ALERT;
            case self::CRITICAL:
                return LOG_CRIT;
            case self::ERROR:
                return LOG_ERR;
            case self::WARNING:
                return LOG_WARNING;
            case self::NOTICE:
                return LOG_NOTICE;
            case self::INFO:
                return LOG_INFO;
            case self::DEBUG:
            default:
                return LOG_DEBUG;
        }
    }
    
    /**
     * Generate JavaScript console logging script
     *
     * @return string JavaScript code for browser console logging
     */
    public static function getConsoleScript()
    {
        return "<script>
            console.log('Logger initialized');
            window.appLogger = {
                debug: function(msg) { console.debug('[DEBUG] ' + msg); },
                info: function(msg) { console.info('[INFO] ' + msg); },
                warn: function(msg) { console.warn('[WARNING] ' + msg); },
                error: function(msg) { console.error('[ERROR] ' + msg); }
            };
        </script>";
    }
}