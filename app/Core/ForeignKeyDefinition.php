class ForeignKeyDefinition {
    protected $column;
    protected $references = 'id';
    protected $on;
    protected $onDelete;
    protected $onUpdate;
    
    public function __construct($column) {
        $this->column = $column;
    }
    
    public function references($column) {
        $this->references = $column;
        return $this;
    }
    
    public function on($table) {
        $this->on = $table;
        return $this;
    }
    
    public function onDelete($action) {
        $this->onDelete = strtoupper($action);
        return $this;
    }
    
    public function onUpdate($action) {
        $this->onUpdate = strtoupper($action);
        return $this;
    }
    
    public function toSQL() {
        if (!$this->on) {
            throw new \RuntimeException('Foreign key must specify target table');
        }
        
        $sql = "FOREIGN KEY ({$this->column}) REFERENCES {$this->on}({$this->references})";
        
        if ($this->onDelete) {
            $sql .= " ON DELETE {$this->onDelete}";
        }
        
        if ($this->onUpdate) {
            $sql .= " ON UPDATE {$this->onUpdate}";
        }
        
        return $sql;
    }}