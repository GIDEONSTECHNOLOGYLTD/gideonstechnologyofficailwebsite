<?php
namespace App\Core;

class Blueprint {
    protected $columns = [];
    protected $indexes = [];
    protected $table;
    protected $foreign = [];
    
    public function id($name = 'id') {
        return $this->addColumn($name, 'SERIAL PRIMARY KEY');
    }

    public function string($name, $length = 255) {
        return $this->addColumn($name, "VARCHAR($length)");
    }

    public function text($name) {
        return $this->addColumn($name, 'TEXT');
    }

    public function foreignId($name) {
        return $this->addColumn($name, 'INTEGER')
            ->addForeignKey($name);
    }

    public function integer($name) {
        return $this->addColumn($name, 'INTEGER');
    }

    public function date($name) {
        return $this->addColumn($name, 'DATE');
    }

    public function bigInteger($name) {
        return $this->addColumn($name, 'BIGINT');
    }

    public function boolean($name) {
        return $this->addColumn($name, 'BOOLEAN');
    }

    public function datetime($name) {
        return $this->addColumn($name, 'TIMESTAMP');
    }

    public function timestamp($name) {
        return $this->addColumn($name, 'TIMESTAMP');
    }

    public function timestamps() {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
    }

    public function addFulltextIndex($columns, $name = null) {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        
        $name = $name ?: 'fulltext_' . implode('_', $columns);
        $this->indexes[] = "CREATE FULLTEXT INDEX {$name} ON " . $this->table . " (" . implode(', ', $columns) . ")";
        return $this;
    }

    public function after($column) {
        $this->columns[count($this->columns) - 1] .= " AFTER {$column}";
        return $this;
    }

    public function addIndex($columns, $name = null) {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        
        $name = $name ?: 'idx_' . implode('_', $columns);
        $this->indexes[] = "CREATE INDEX IF NOT EXISTS {$name} ON {$this->table}(" . implode(', ', $columns) . ")";
        return $this;
    }

    public function softDeletes() {
        $this->timestamp('deleted_at')->nullable();
    }

    public function dropForeign($columns) {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        foreach ($columns as $column) {
            $this->columns[] = "DROP FOREIGN KEY fk_{$this->table}_{$column}";
        }
        return $this;
    }

    public function decimal($name, $total = 8, $places = 2) {
        return $this->addColumn($name, "DECIMAL($total,$places)");
    }

    public function float($name) {
        return $this->addColumn($name, 'FLOAT');
    }

    public function json($name) {
        return $this->addColumn($name, 'JSONB');
    }

    public function enum($name, array $values) {
        $valuesStr = implode("','", $values);
        return $this->addColumn($name, "ENUM('$valuesStr')");
    }

    public function foreign($column) {
        $foreign = new ForeignKeyDefinition($column);
        $this->foreign[] = $foreign;
        return $foreign;
    }

    public function index($columns, $name = null) {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->indexes[] = compact('name', 'columns');
    }

    public function unique($columns, $name = null) {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->indexes[] = [
            'columns' => $columns,
            'name' => $name,
            'unique' => true
        ];
    }

    protected function addColumn($name, $type) {
        $column = new ColumnDefinition($name, $type);
        $this->columns[] = $column;
        return $column;
    }

    public function toSQL() {
        $statements = [];

        if (!empty($this->columns)) {
            $alterTable = sprintf('ALTER TABLE %s %s', $this->table, implode(', ', $this->columns));
            $statements[] = $alterTable;
        }

        foreach ($this->indexes as $index) {
            $statements[] = $index;
        }

        foreach ($this->foreign as $foreign) {
            $statements[] = $foreign;
        }

        return $statements;
    }
}