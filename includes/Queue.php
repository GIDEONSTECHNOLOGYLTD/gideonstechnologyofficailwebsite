<?php
class Queue {
    private $db;
    private $table = 'jobs';
    private $availableQueues = ['default', 'emails', 'notifications', 'uploads'];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function push($job, $data = [], $queue = 'default', $delay = 0) {
        if (!in_array($queue, $this->availableQueues)) {
            throw new Exception('Invalid queue specified');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (queue, job, data, available_at, created_at) 
             VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND), NOW())"
        );

        $jsonData = json_encode($data);
        $stmt->bind_param('sssi', $queue, $job, $jsonData, $delay);
        return $stmt->execute();
    }

    public function process($queue = 'default', $limit = 10) {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM {$this->table} 
                 WHERE queue = ? 
                 AND available_at <= NOW() 
                 AND reserved_at IS NULL 
                 AND failed_at IS NULL 
                 LIMIT ?"
            );
            
            $stmt->bind_param('si', $queue, $limit);
            $stmt->execute();
            $jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            foreach ($jobs as $job) {
                $this->processJob($job);
            }

            $this->db->commit();
            return count($jobs);

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function processJob($job) {
        // Mark job as reserved
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET reserved_at = NOW() 
             WHERE id = ? AND reserved_at IS NULL"
        );
        $stmt->bind_param('i', $job['id']);
        $stmt->execute();

        try {
            $handler = $this->getJobHandler($job['job']);
            $data = json_decode($job['data'], true);
            
            $handler->handle($data);

            // Delete completed job
            $this->delete($job['id']);

        } catch (Exception $e) {
            $this->markAsFailed($job['id'], $e->getMessage());
            throw $e;
        }
    }

    private function getJobHandler($job) {
        $class = ucfirst($job) . 'Job';
        if (!class_exists($class)) {
            throw new Exception("Job handler class {$class} not found");
        }
        return new $class();
    }

    private function markAsFailed($jobId, $error) {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET failed_at = NOW(), 
                 error = ? 
             WHERE id = ?"
        );
        $stmt->bind_param('si', $error, $jobId);
        return $stmt->execute();
    }

    public function retry($jobId) {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET failed_at = NULL, 
                 error = NULL, 
                 reserved_at = NULL, 
                 available_at = NOW(), 
                 attempts = attempts + 1 
             WHERE id = ?"
        );
        $stmt->bind_param('i', $jobId);
        return $stmt->execute();
    }

    public function delete($jobId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $jobId);
        return $stmt->execute();
    }

    public function flush($queue = null) {
        if ($queue) {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE queue = ?");
            $stmt->bind_param('s', $queue);
            return $stmt->execute();
        }
        
        return $this->db->query("DELETE FROM {$this->table}");
    }

    public function getFailedJobs($limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} 
             WHERE failed_at IS NOT NULL 
             ORDER BY failed_at DESC 
             LIMIT ?"
        );
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getQueueSize($queue = 'default') {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count 
             FROM {$this->table} 
             WHERE queue = ? 
             AND reserved_at IS NULL 
             AND failed_at IS NULL"
        );
        $stmt->bind_param('s', $queue);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }
}