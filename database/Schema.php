<?php

namespace Database;

use PDO;
use PDOException;
use RuntimeException;

class Schema {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($table, $callback) {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $sql = $blueprint->getSql();
        $this->execute($sql);
    }

    public function table($table, $callback) {
        $table = $this->validateTableName($table);
        $blueprint = new Blueprint($table, true); // true indicates altering existing table
        $callback($blueprint);
        $sql = $blueprint->getSql();
        $this->execute($sql);
    }

    public function createRaw($sqlStatements) {
        if (!is_array($sqlStatements)) {
            $sqlStatements = [$sqlStatements];
        }
        
        foreach ($sqlStatements as $sql) {
            $this->execute($sql);
        }
    }

    public function dropRaw($sqlStatements) {
        if (!is_array($sqlStatements)) {
            $sqlStatements = [$sqlStatements];
        }
        
        foreach ($sqlStatements as $sql) {
            $this->execute($sql);
        }
    }

    public function drop($tables) {
        if (!is_array($tables)) {
            $tables = [$tables];
        }
        foreach ($tables as $table) {
            $this->execute("DROP TABLE IF EXISTS {$table}");
        }
    }

    public function dropIfExists($table) {
        return $this->drop($table);
    }

    public function hasColumn($table, $column) {
        $table = $this->validateTableName($table);
        $stmt = $this->pdo->prepare("PRAGMA table_info({$table})");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $col) {
            if ($col['name'] == $column) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function validateTableName($table) {
        // Simple validation to prevent SQL injection in table names
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new RuntimeException("Invalid table name: {$table}");
        }
        return $table;
    }

    protected function execute($sql) {
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to execute SQL: " . $e->getMessage() . "\nSQL: " . $sql);
        }
    }
}
