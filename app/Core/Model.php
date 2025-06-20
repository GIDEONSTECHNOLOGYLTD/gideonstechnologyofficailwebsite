abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $casts = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $attributes = [];

    public function __construct(array $attributes = []) {
        $this->db = Database::getInstance()->getConnection();
        $this->fill($attributes);
    }

    public function fill(array $attributes) {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->setAttribute($key, $value);
            }
        }
    }

    public function setAttribute($key, $value) {
        if (in_array($key, $this->dates)) {
            $value = new \DateTime($value);
        }

        if (isset($this->casts[$key])) {
            $value = $this->castAttribute($key, $value);
        }

        $this->attributes[$key] = $value;
    }

    public function getAttribute($key) {
        if (!isset($this->attributes[$key])) {
            return null;
        }

        $value = $this->attributes[$key];

        if (in_array($key, $this->dates) && !$value instanceof \DateTime) {
            return new \DateTime($value);
        }

        if (isset($this->casts[$key])) {
            return $this->castAttribute($key, $value);
        }

        return $value;
    }

    protected function castAttribute($key, $value) {
        switch ($this->casts[$key]) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'object':
                return json_decode($value);
            case 'date':
                return new \DateTime($value);
            default:
                return $value;
        }
    }

    public function save() {
        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update();
        }
        return $this->insert();
    }

    protected function insert() {
        $this->attributes['created_at'] = date('Y-m-d H:i:s');
        $this->attributes['updated_at'] = date('Y-m-d H:i:s');

        $fields = array_keys($this->attributes);
        $values = array_fill(0, count($fields), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            implode(', ', $values)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($this->attributes));

        $this->attributes[$this->primaryKey] = $this->db->lastInsertId();
        return true;
    }

    protected function update() {
        $this->attributes['updated_at'] = date('Y-m-d H:i:s');

        $fields = [];
        $values = [];

        foreach ($this->attributes as $key => $value) {
            if ($key !== $this->primaryKey) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }

        $values[] = $this->attributes[$this->primaryKey];

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', $fields),
            $this->primaryKey
        );

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete() {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        $sql = sprintf(
            "DELETE FROM %s WHERE %s = ?",
            $this->table,
            $this->primaryKey
        );

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->attributes[$this->primaryKey]]);
    }

    public function toArray() {
        $array = [];
        foreach ($this->attributes as $key => $value) {
            if (!in_array($key, $this->hidden)) {
                $array[$key] = $this->getAttribute($key);
            }
        }
        return $array;
    }

    public function jsonSerialize() {
        return $this->toArray();
    }

    public function __get($key) {
        return $this->getAttribute($key);
    }

    public function __set($key, $value) {
        $this->setAttribute($key, $value);
    }

    public function __isset($key) {
        return isset($this->attributes[$key]);
    }

    public function __unset($key) {
        unset($this->attributes[$key]);
    }
}