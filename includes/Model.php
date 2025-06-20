<?php
abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = ['password'];
    protected $timestamps = true;

    public function __construct() {
        $this->db = Database::getInstance();
        if (!$this->table) {
            $this->table = strtolower(get_class($this)) . 's';
        }
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $this->processResult($stmt->get_result()->fetch_assoc());
    }

    public function create(array $data) {
        $data = $this->filterFillable($data);
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $types = str_repeat('s', count($data));
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($values)");
        $stmt->bind_param($types, ...array_values($data));
        
        if ($stmt->execute()) {
            return $this->find($stmt->insert_id);
        }
        return false;
    }

    public function update($id, array $data) {
        $data = $this->filterFillable($data);
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $types = str_repeat('s', count($data)) . 'i';
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?");
        $values = array_values($data);
        $values[] = $id;
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    public function all() {
        $result = $this->db->query("SELECT * FROM {$this->table}");
        return array_map([$this, 'processResult'], $result->fetch_all(MYSQLI_ASSOC));
    }

    public function where($conditions, $params = [], $operator = 'AND') {
        $where = implode(" $operator ", array_map(function($field) {
            return "$field = ?";
        }, array_keys($conditions)));

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $where");
        $types = str_repeat('s', count($params));
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        
        return array_map([$this, 'processResult'], $stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    }

    public function firstWhere($conditions, $params = []) {
        $results = $this->where($conditions, $params);
        return !empty($results) ? $results[0] : null;
    }

    protected function filterFillable($data) {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function processResult($result) {
        if (!$result) return null;
        
        foreach ($this->hidden as $field) {
            unset($result[$field]);
        }
        
        return $result;
    }

    public function paginate($page = 1, $perPage = DEFAULT_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $total = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}")
                         ->fetch_assoc()['count'];
        
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} LIMIT ?, ?");
        $stmt->bind_param('ii', $offset, $perPage);
        $stmt->execute();
        
        $results = array_map([$this, 'processResult'], $stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        
        return [
            'data' => $results,
            'pagination' => (new Paginator($total, $page, $perPage))->toArray()
        ];
    }

    public function beginTransaction() {
        $this->db->beginTransaction();
    }

    public function commit() {
        $this->db->commit();
    }

    public function rollback() {
        $this->db->rollback();
    }
}