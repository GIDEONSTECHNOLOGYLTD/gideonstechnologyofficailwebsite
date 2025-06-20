<?php

namespace Database;

use PDO;
use PDOException;

class DatabaseSeeder {
    protected $pdo;
    protected $seeders = [];

    public function __construct() {
        $this->connect();
    }

    protected function connect() {
        $dbPath = dirname(__DIR__) . '/database/gtech.db';
        try {
            $this->pdo = new PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('PRAGMA foreign_keys = ON');
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public function register($seeder) {
        $this->seeders[] = $seeder;
        return $this;
    }

    public function run() {
        // If no specific seeders registered, find and run all seeders
        if (empty($this->seeders)) {
            $this->findSeeders();
        }
        
        foreach ($this->seeders as $seeder) {
            if (is_string($seeder)) {
                // If it's a class name, instantiate it
                $class = $seeder;
                $seeder = new $class($this->pdo);
            }
            
            if (method_exists($seeder, 'run')) {
                echo "Running seeder: " . get_class($seeder) . "\n";
                $seeder->run();
            }
        }
    }

    protected function findSeeders() {
        $pattern = dirname(__DIR__) . '/database/seeds/*.php';
        $files = glob($pattern);
        
        foreach ($files as $file) {
            require_once $file;
            
            $className = pathinfo($file, PATHINFO_FILENAME);
            $fullClassName = "\\Database\\Seeds\\{$className}";
            
            if (class_exists($fullClassName)) {
                $this->register($fullClassName);
            }
        }
    }
}