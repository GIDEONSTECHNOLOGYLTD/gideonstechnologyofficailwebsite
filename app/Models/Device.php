<?php
namespace App\Models;

class Device extends BaseModel {
    protected $table = 'devices';
    public function register($userId, $token, $platform) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (
                user_id, device_token, platform, created_at
            ) VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
                device_token = VALUES(device_token),
                updated_at = NOW()
        ");
        return $stmt->execute([$userId, $token, $platform]);
    }
    public function getUserDevices($userId) {
        $stmt = $this->db->prepare("\n            SELECT * FROM {$this->table}\n            WHERE user_id = ? AND active = 1\n        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    public function deactivate($token) {
        $stmt = $this->db->prepare("\n            UPDATE {$this->table}\n            SET active = 0, updated_at = NOW()\n            WHERE device_token = ?\n        ");
        return $stmt->execute([$token]);
    }
}
