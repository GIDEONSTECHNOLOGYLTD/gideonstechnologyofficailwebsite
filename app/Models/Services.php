<?php
namespace App\Models;

class Services {
    private $db;

    public function __construct() {
        $this->db = \App\Core\App::getInstance()->resolve('db');
    }

    public function getFeaturedServices() {
        return [
            [
                'id' => 1,
                'name' => 'Web Development',
                'description' => 'Custom web applications and websites built with modern technologies.',
                'image' => '/assets/img/services/web-dev.jpg',
                'featured' => true
            ]
        ];
    }

    public function getAllServices() {
        try {
            $stmt = $this->db->prepare('SELECT * FROM services ORDER BY category, name');
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error in getAllServices: ' . $e->getMessage());
            return [];
        }
    }

    public function getServicesByCategory($category) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM services WHERE category = ? ORDER BY name');
            $stmt->execute([$category]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error in getServicesByCategory: ' . $e->getMessage());
            return [];
        }
    }

    public function getServiceById($id) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM services WHERE id = ?');
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error in getServiceById: ' . $e->getMessage());
            return null;
        }
    }

    public function createService($data) {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO services (name, description, category, price, image, featured) 
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $data['name'],
                $data['description'],
                $data['category'],
                $data['price'],
                $data['image'],
                $data['featured']
            ]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log('Error in createService: ' . $e->getMessage());
            return false;
        }
    }

    public function updateService($id, $data) {
        try {
            $stmt = $this->db->prepare(
                'UPDATE services SET name = ?, description = ?, category = ?, 
                 price = ?, image = ?, featured = ? WHERE id = ?'
            );
            $stmt->execute([
                $data['name'],
                $data['description'],
                $data['category'],
                $data['price'],
                $data['image'],
                $data['featured'],
                $id
            ]);
            return true;
        } catch (\PDOException $e) {
            error_log('Error in updateService: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteService($id) {
        try {
            $stmt = $this->db->prepare('DELETE FROM services WHERE id = ?');
            $stmt->execute([$id]);
            return true;
        } catch (\PDOException $e) {
            error_log('Error in deleteService: ' . $e->getMessage());
            return false;
        }
    }
}
