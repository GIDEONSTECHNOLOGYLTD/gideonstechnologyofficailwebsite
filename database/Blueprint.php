<?php

namespace Database;

class Blueprint {
    protected $table;
    protected $columns = [];
    protected $indexes = [];
    protected $foreignKeys = [];
    protected $isAltering = false;
    protected $currentColumn;

    public function __construct($table, $isAltering = false) {
        $this->table = $table;
        $this->isAltering = $isAltering;
    }

    public function id() {
        $this->columns[] = "id INTEGER PRIMARY KEY AUTOINCREMENT";
        return $this;
    }

    public function integer($name) {
        $this->currentColumn = $name;
        $this->columns[] = "{$name} INTEGER";
        return $this;
    }

    public function string($name, $length = 255) {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }

    public function text($name) {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }

    public function decimal($name, $precision = 8, $scale = 2) {
        $this->columns[] = "{$name} DECIMAL({$precision},{$scale})";
        return $this;
    }

    public function boolean($name) {
        $this->columns[] = "{$name} BOOLEAN";
        return $this;
    }

    public function datetime($name) {
        $this->columns[] = "{$name} DATETIME";
        return $this;
    }

    public function timestamp($name) {
        $this->columns[] = "{$name} TIMESTAMP";
        return $this;
    }

    public function unique($fields) {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $name = "idx_" . $this->table . "_" . implode('_', $fields) . "_unique";
        $this->indexes[] = "CONSTRAINT {$name} UNIQUE (" . implode(',', $fields) . ")";
        return $this;
    }

    public function index($fields) {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $name = "idx_" . $this->table . "_" . implode('_', $fields);
        $this->indexes[] = "CREATE INDEX {$name} ON {$this->table} (" . implode(',', $fields) . ")";
        return $this;
    }

    public function primary($fields) {
        if (!is_array($fields)) {
            $fields = [$fields];
        }
        $this->indexes[] = "PRIMARY KEY (" . implode(',', $fields) . ")";
        return $this;
    }

    public function foreign($column) {
        $this->currentColumn = $column;
        return $this;
    }

    public function references($table, $column = 'id') {
        $this->foreignKeys[] = "FOREIGN KEY ({$this->currentColumn}) REFERENCES {$table}({$column})";
        return $this;
    }

    public function onDelete($action) {
        $lastForeignKey = end($this->foreignKeys);
        if ($lastForeignKey) {
            $this->foreignKeys[count($this->foreignKeys) - 1] = $lastForeignKey . " ON DELETE {$action}";
        }
        return $this;
    }

    public function timestamps() {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
        return $this;
    }

    public function nullable() {
        $lastIndex = count($this->columns) - 1;
        if ($lastIndex >= 0) {
            $this->columns[$lastIndex] .= " NULL";
        }
        return $this;
    }

    public function default($value) {
        $lastIndex = count($this->columns) - 1;
        if ($lastIndex >= 0) {
            if (is_string($value) && !is_numeric($value)) {
                $value = "'".$value."'";
            } elseif (is_bool($value)) {
                $value = $value ? 1 : 0;
            }
            $this->columns[$lastIndex] .= " DEFAULT {$value}";
        }
        return $this;
    }

    public function enum($name, $values) {
        $this->columns[] = "{$name} TEXT CHECK({$name} IN ('" . implode("', '", $values) . "'))";
        return $this;
    }

    public function dropColumn($name) {
        if ($this->isAltering) {
            $this->columns[] = "DROP COLUMN {$name}";
        }
        return $this;
    }

    public function getSql() {
        if ($this->isAltering) {
            $sql = "ALTER TABLE {$this->table} ";
            $sql .= implode(', ', $this->columns);
            return $sql;
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (";
            $sql .= implode(', ', $this->columns);

            if (!empty($this->indexes)) {
                $sql .= ', ' . implode(', ', $this->indexes);
            }

            if (!empty($this->foreignKeys)) {
                $sql .= ', ' . implode(', ', $this->foreignKeys);
            }

            $sql .= ")";
            return $sql;
        }
    }

    public function splitSql() {
        $tableSql = $this->getSql();
        $indexesSql = null;

        return [$tableSql, $indexesSql];
    }
}
