<?php
namespace App\Models;

class Transaction extends BaseModel {
    protected $table = 'transactions';
    public function getUserTransactions($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (
                    user_id, type, amount, status, reference, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['user_id'],
                $data['type'],
                $data['amount'],
                'pending',
                $this->generateReference()
            ]);
            $this->db->commit();
            return $this->db->lastInsertId();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function generateReference() {
        return 'TXN' . time() . rand(1000, 9999);
    }
}
