<?php
namespace App\Repositories;

use PDO;
use App\Core\Database;

class TemplateRepository {
    protected $db;
    protected $table = 'templates';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getByCategory(string $category): array {
        $sql = "SELECT * FROM {$this->table} WHERE category = ? ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getFeatured(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT 6";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById(int $id) {
        $sql = "SELECT t.*, c.name as category_name"
             . " FROM {$this->table} t"
             . " JOIN template_categories c ON t.category = c.slug"
             . " WHERE t.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function purchase(int $templateId, int $userId): bool {
        $sql = "INSERT INTO template_purchases (template_id, user_id, purchase_date) VALUES (?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$templateId, $userId]);
    }

    public function getPurchased(int $userId): array {
        $sql = "SELECT t.*, tp.id AS purchase_id, tp.purchase_date"
             . " FROM {$this->table} t"
             . " JOIN template_purchases tp ON t.id = tp.template_id"
             . " WHERE tp.user_id = ?"
             . " ORDER BY tp.purchase_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Fetch a single purchase record for a user
     */
    public function getPurchaseById(int $purchaseId, int $userId) {
        $sql = "SELECT tp.id AS purchase_id, tp.purchase_date, t.*"
             . " FROM {$this->table} t"
             . " JOIN template_purchases tp ON t.id = tp.template_id"
             . " WHERE tp.id = ? AND tp.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$purchaseId, $userId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
