<?php

namespace App\Core\Database;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $primary = null;
    protected $foreign = [];
    protected $indexes = [];

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function id($column = 'id')
    {
        $this->integer($column, true)->autoIncrement()->primary();
        return $this;
    }

    public function string($column, $length = 255)
    {
        $this->columns[$column] = [
            'type' => 'TEXT',
            'length' => $length,
            'nullable' => false
        ];

        return $this;
    }

    public function integer($column, $autoIncrement = false)
    {
        $this->columns[$column] = [
            'type' => 'INTEGER',
            'autoIncrement' => $autoIncrement,
            'nullable' => false
        ];

        return $this;
    }

    public function text($column)
    {
        $this->columns[$column] = [
            'type' => 'TEXT',
            'nullable' => false
        ];

        return $this;
    }

    public function float($column)
    {
        $this->columns[$column] = [
            'type' => 'REAL',
            'nullable' => false
        ];

        return $this;
    }

    public function boolean($column)
    {
        $this->columns[$column] = [
            'type' => 'INTEGER',
            'nullable' => false
        ];

        return $this;
    }

    public function timestamp($column)
    {
        $this->columns[$column] = [
            'type' => 'TIMESTAMP',
            'nullable' => false
        ];

        return $this;
    }

    public function timestamps()
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
        
        return $this;
    }

    public function nullable()
    {
        $column = array_key_last($this->columns);
        $this->columns[$column]['nullable'] = true;
        
        return $this;
    }

    public function unique()
    {
        $column = array_key_last($this->columns);
        $this->indexes[] = [
            'type' => 'UNIQUE',
            'columns' => [$column]
        ];
        
        return $this;
    }

    public function autoIncrement()
    {
        $column = array_key_last($this->columns);
        $this->columns[$column]['autoIncrement'] = true;
        
        return $this;
    }

    public function primary()
    {
        $column = array_key_last($this->columns);
        $this->primary = $column;
        
        return $this;
    }

    public function foreignKey($column, $reference, $onDelete = 'CASCADE', $onUpdate = 'CASCADE')
    {
        $this->foreign[$column] = [
            'reference' => $reference,
            'onDelete' => $onDelete,
            'onUpdate' => $onUpdate
        ];
        
        return $this;
    }

    public function toSql()
    {
        $sql = "CREATE TABLE {$this->table} (\n";
        $columnDefinitions = [];

        foreach ($this->columns as $name => $definition) {
            $columnSql = "  $name {$definition['type']}";
            
            if (isset($definition['length'])) {
                $columnSql .= "({$definition['length']})";
            }
            
            if (isset($definition['autoIncrement']) && $definition['autoIncrement']) {
                $columnSql .= " AUTOINCREMENT";
            }
            
            if ($this->primary === $name) {
                $columnSql .= " PRIMARY KEY";
            }
            
            if (!$definition['nullable']) {
                $columnSql .= " NOT NULL";
            }
            
            $columnDefinitions[] = $columnSql;
        }

        // Add foreign key constraints
        foreach ($this->foreign as $column => $definition) {
            list($referenceTable, $referenceColumn) = explode('.', $definition['reference']);
            $foreignSql = "  FOREIGN KEY ($column) REFERENCES $referenceTable($referenceColumn)";
            
            if ($definition['onDelete']) {
                $foreignSql .= " ON DELETE {$definition['onDelete']}";
            }
            
            if ($definition['onUpdate']) {
                $foreignSql .= " ON UPDATE {$definition['onUpdate']}";
            }
            
            $columnDefinitions[] = $foreignSql;
        }

        // Add unique constraints
        foreach ($this->indexes as $index) {
            if ($index['type'] === 'UNIQUE') {
                $columnDefinitions[] = "  UNIQUE (" . implode(', ', $index['columns']) . ")";
            }
        }

        $sql .= implode(",\n", $columnDefinitions);
        $sql .= "\n)";

        return $sql;
    }
}
