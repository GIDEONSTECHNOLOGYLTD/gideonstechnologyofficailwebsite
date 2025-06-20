<?php
namespace App\Models;

class RepairRequest extends BaseModel {
    protected $table = 'repair_requests';
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (
                name, email, device_type, issue, 
                status, created_at
            ) VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['device_type'],
            $data['issue']
        ]);
    }
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("\n            UPDATE {$this->table}\n            SET status = ?, updated_at = NOW()\n            WHERE id = ?\n        ");
        return $stmt->execute([$status, $id]);
    }
    public function getPending() {
        $stmt = $this->db->prepare("\n            SELECT * FROM {$this->table}\n            WHERE status = 'pending'\n            ORDER BY created_at ASC\n        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getByStatus($status) {
        $stmt = $this->db->prepare("\n            SELECT * FROM {$this->table}\n            WHERE status = ?\n            ORDER BY created_at DESC\n        ");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
}
