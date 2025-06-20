<?php
namespace App\Models;

class Template extends BaseModel
{
    protected $table = 'templates';

    public function getFeatured()
    {
        return $this->db->query(
"SELECT * FROM {$this->table}\n            WHERE featured = 1 AND status = 'active'\n            ORDER BY created_at DESC\n            LIMIT 6"
        )->fetchAll();
    }

    public function getAll()
    {
        return $this->db->query(
"SELECT * FROM {$this->table}\n            WHERE status = 'active'\n            ORDER BY created_at DESC"
        )->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare(
"SELECT * FROM {$this->table}\n            WHERE id = ? AND status = 'active'\n            LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function purchase($templateId, $userId)
    {
        $stmt = $this->db->prepare(
"INSERT INTO template_purchases (\n                template_id, user_id, created_at\n            ) VALUES (?, ?, NOW())"
        );
        return $stmt->execute([$templateId, $userId]);
    }

    public function getPurchased($userId)
    {
        $stmt = $this->db->prepare(
"SELECT t.*\n            FROM {$this->table} t\n            JOIN template_purchases tp ON t.id = tp.template_id\n            WHERE tp.user_id = ?\n            ORDER BY tp.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
