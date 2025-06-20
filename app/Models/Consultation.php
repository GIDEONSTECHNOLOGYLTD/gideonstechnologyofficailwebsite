<?php
namespace App\Models;

class Consultation extends BaseModel {
    protected $table = 'consultations';
    public function schedule($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (
                name, email, description, status, created_at
            ) VALUES (?, ?, ?, 'pending', NOW())
        ");
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['description']
        ]);
    }
    public function getUserConsultations($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$status, $id]);
        return $stmt->rowCount();
    }
}
