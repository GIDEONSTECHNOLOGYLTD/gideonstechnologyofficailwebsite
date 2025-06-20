<?php
class Database {
    private static $instance = null;
    private $connection;
    private $transactions = 0;
    private $queryLog = [];
    private $inTransaction = false;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        $this->connection = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME,
            DB_PORT
        );

        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }

        $this->connection->set_charset('utf8mb4');
    }

    public function query($sql, $params = []) {
        $start = microtime(true);
        $stmt = $this->prepare($sql);

        if (!empty($params)) {
            $types = $this->getParamTypes($params);
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();
        $result = $stmt->get_result();
        $this->logQuery($sql, $params, microtime(true) - $start);

        if (!$success) {
            throw new Exception("Query failed: " . $stmt->error);
        }

        return $result;
    }

    public function prepare($sql) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        return $stmt;
    }

    private function getParamTypes($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        return $types;
    }

    public function fetch($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_assoc();
    }

    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        
        $this->query($sql, array_values($data));
        return $this->connection->insert_id;
    }

    public function update($table, $data, $where, $whereParams = []) {
        $set = implode(', ', array_map(function($column) {
            return "{$column} = ?";
        }, array_keys($data)));
        
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $params = array_merge(array_values($data), $whereParams);
        
        $this->query($sql, $params);
        return $this->connection->affected_rows;
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $this->query($sql, $params);
        return $this->connection->affected_rows;
    }

    public function beginTransaction() {
        if (!$this->inTransaction) {
            $this->connection->begin_transaction();
            $this->inTransaction = true;
        }
        $this->transactions++;
    }

    public function commit() {
        if (!$this->inTransaction) {
            return;
        }

        $this->transactions--;
        if ($this->transactions === 0) {
            $this->connection->commit();
            $this->inTransaction = false;
        }
    }

    public function rollback() {
        if (!$this->inTransaction) {
            return;
        }

        $this->connection->rollback();
        $this->transactions = 0;
        $this->inTransaction = false;
    }

    private function logQuery($sql, $params, $time) {
        $this->queryLog[] = [
            'sql' => $sql,
            'params' => $params,
            'time' => $time
        ];
    }

    public function getQueryLog() {
        return $this->queryLog;
    }

    public function clearQueryLog() {
        $this->queryLog = [];
    }

    public function getConnection() {
        return $this->connection;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function __destruct() {
        $this->close();
    }
}
?>