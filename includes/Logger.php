<?php
class Logger {
    private static $instance = null;
    private $logFile;
    private $logLevel;

    const DEBUG = 100;
    const INFO = 200;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;
    const CRITICAL = 500;
    const ALERT = 550;
    const EMERGENCY = 600;

    private function __construct($logFile, $logLevel = self::DEBUG) {
        $this->logFile = $logFile;
        $this->logLevel = $logLevel;
    }

    public static function getInstance($logFile = null, $logLevel = null) {
        if (self::$instance === null) {
            $logFile = $logFile ?? dirname(__DIR__) . '/logs/app.log';
            $logLevel = $logLevel ?? self::DEBUG;
            self::$instance = new self($logFile, $logLevel);
        }
        return self::$instance;
    }

    public function log($level, $message, array $context = []) {
        if ($level < $this->logLevel) {
            return;
        }

        $levelName = $this->getLevelName($level);
        $timestamp = date('Y-m-d H:i:s');
        $message = $this->interpolate($message, $context);
        
        $logEntry = sprintf(
            "[%s] %s: %s\n",
            $timestamp,
            $levelName,
            $message
        );

        $this->write($logEntry);
    }

    private function write($message) {
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents($this->logFile, $message, FILE_APPEND | LOCK_EX);
    }

    private function interpolate($message, array $context = []) {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || method_exists($val, '__toString')) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }

    private function getLevelName($level) {
        $levels = [
            self::DEBUG => 'DEBUG',
            self::INFO => 'INFO',
            self::NOTICE => 'NOTICE',
            self::WARNING => 'WARNING',
            self::ERROR => 'ERROR',
            self::CRITICAL => 'CRITICAL',
            self::ALERT => 'ALERT',
            self::EMERGENCY => 'EMERGENCY'
        ];
        return $levels[$level] ?? 'UNKNOWN';
    }

    public function debug($message, array $context = []) {
        $this->log(self::DEBUG, $message, $context);
    }

    public function info($message, array $context = []) {
        $this->log(self::INFO, $message, $context);
    }

    public function notice($message, array $context = []) {
        $this->log(self::NOTICE, $message, $context);
    }

    public function warning($message, array $context = []) {
        $this->log(self::WARNING, $message, $context);
    }

    public function error($message, array $context = []) {
        $this->log(self::ERROR, $message, $context);
    }

    public function critical($message, array $context = []) {
        $this->log(self::CRITICAL, $message, $context);
    }

    public function alert($message, array $context = []) {
        $this->log(self::ALERT, $message, $context);
    }

    public function emergency($message, array $context = []) {
        $this->log(self::EMERGENCY, $message, $context);
    }

    public function setLogLevel($level) {
        $this->logLevel = $level;
    }

    public function getLogLevel() {
        return $this->logLevel;
    }
}