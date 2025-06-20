<?php

namespace Database;

use PDO;
use PDOException;
use RuntimeException;

class Migration {
    protected $schema;
    protected $pdo;

    public function __construct() {
        $this->connect();
        $this->schema = new Schema($this->pdo);
    }

    protected function connect() {
        $dbPath = dirname(__DIR__) . '/database/gtech.db';
        try {
            $this->pdo = new PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('PRAGMA foreign_keys = ON');
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public function up() {
        // This method should be implemented by child classes
        throw new RuntimeException('up() method must be implemented by child classes');
    }

    public function down() {
        // This method should be implemented by child classes
        throw new RuntimeException('down() method must be implemented by child classes');
    }

    protected function execute($sql) {
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to execute SQL: " . $e->getMessage() . "\nSQL: " . $sql);
        }
    }
}
