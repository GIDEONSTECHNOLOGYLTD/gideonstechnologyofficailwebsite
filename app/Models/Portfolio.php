<?php
namespace App\Models;

class Portfolio extends BaseModel {
    protected $table = 'portfolio_works';
    public function getFeatured() {
        return $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE featured = 1 AND status = 'active'
            ORDER BY created_at DESC LIMIT 6
        ")->fetchAll();
    }
    public function getAll($category = null) {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active'";
        if ($category) {
            $stmt = $this->db->prepare($sql . " AND category = ? ORDER BY created_at DESC");
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql . " ORDER BY completion_date DESC")->fetchAll();
    }
}
