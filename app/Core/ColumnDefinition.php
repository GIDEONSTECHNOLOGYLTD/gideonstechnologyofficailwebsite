<?php

namespace App\Core;

class ColumnDefinition {
    protected $name;
    protected $type;
    protected $nullable = false;
    protected $default = null;
    protected $unique = false;
    protected $unsigned = false;
    
    public function __construct($name, $type) {
        $this->name = $name;
        $this->type = $type;
    }
    
    public function nullable() {
        $this->nullable = true;
        return $this;
    }
    
    public function default($value) {
        $this->default = $value;
        return $this;
    }
    
    public function unique() {
        $this->unique = true;
        return $this;
    }
    
    public function unsigned() {
        $this->unsigned = true;
        return $this;
    }
    
    public function toSQL() {
        $sql = "{$this->name} {$this->type}";
        
        if ($this->unsigned) {
            $sql .= " UNSIGNED";
        }
        
        if (!$this->nullable) {
            $sql .= " NOT NULL";
        }
        
        if ($this->default !== null) {
            $sql .= " DEFAULT " . $this->formatDefault($this->default);
        }
        
        if ($this->unique) {
            $sql .= " UNIQUE";
        }
        
        return $sql;
    }
    
    protected function formatDefault($value) {
        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }
        
        if (is_numeric($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
        }
        
        if ($value instanceof \DateTime) {
            return "'" . $value->format('Y-m-d H:i:s') . "'";
        }
        
        if (is_null($value)) {
            return 'NULL';
        }
        
        throw new \InvalidArgumentException('Unsupported default value type');
    }}