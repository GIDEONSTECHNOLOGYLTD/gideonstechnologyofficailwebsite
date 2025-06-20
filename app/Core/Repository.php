abstract class Repository {
    protected $db;
    protected $table;
    protected $model;
    protected $cache;
    protected $cacheTTL = 3600;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->cache = new Cache();
    }

    public function find($id) {
        $cacheKey = $this->getCacheKey(__FUNCTION__, $id);
        
        return $this->cache->remember($cacheKey, $this->cacheTTL, function() use ($id) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        });
    }

    public function all() {
        $cacheKey = $this->getCacheKey(__FUNCTION__);
        
        return $this->cache->remember($cacheKey, $this->cacheTTL, function() {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll();
        });
    }

    public function create(array $data) {
        try {
            $this->db->beginTransaction();
            
            $fields = implode(', ', array_keys($data));
            $values = implode(', ', array_fill(0, count($data), '?'));
            
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} ({$fields}) 
                VALUES ({$values})
            ");
            
            $stmt->execute(array_values($data));
            $id = $this->db->lastInsertId();
            
            $this->db->commit();
            $this->clearCache();
            
            return $this->find($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, array $data) {
        try {
            $this->db->beginTransaction();
            
            $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
            $values = array_values($data);
            $values[] = $id;
            
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET {$fields} 
                WHERE id = ?
            ");
            
            $result = $stmt->execute($values);
            
            $this->db->commit();
            $this->clearCache();
            
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            $this->db->commit();
            $this->clearCache();
            
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function paginate($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT SQL_CALC_FOUND_ROWS * 
            FROM {$this->table} 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$perPage, $offset]);
        $items = $stmt->fetchAll();
        
        $totalStmt = $this->db->query("SELECT FOUND_ROWS()");
        $total = $totalStmt->fetchColumn();
        
        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    public function where($conditions, $params = []) {
        $where = implode(' AND ', array_map(function($field) {
            return "$field = ?";
        }, array_keys($conditions)));
        
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE {$where}
        ");
        
        $stmt->execute(array_values($conditions));
        return $stmt->fetchAll();
    }

    protected function getCacheKey($method, ...$params) {
        return sprintf(
            '%s:%s:%s:%s',
            get_class($this),
            $method,
            $this->table,
            md5(serialize($params))
        );
    }

    protected function clearCache() {
        return $this->cache->clear(get_class($this) . ':*');
    }