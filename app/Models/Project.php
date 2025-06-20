<?php
namespace App\Models;

class Project extends BaseModel {
    protected $table = 'projects';
    public function getRecent($limit = 6) {
        return $this->db->query("
            SELECT * FROM {$this->table} 
            WHERE status = 'active' 
            ORDER BY completion_date DESC 
            LIMIT {$limit}
        ")->fetchAll();
    }
    public function getAll() {
        return $this->db->query("
            SELECT * FROM {$this->table}
            ORDER BY completion_date DESC
        ")->fetchAll();
    }
}
