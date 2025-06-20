<?php

namespace App\Models;

use App\Core\Database;

/**
 * Testimonial Model
 * Handles business logic related to customer testimonials
 */
class Testimonial extends BaseModel
{
    protected $table = 'testimonials';
    protected $fillable = [
        'client_name', 
        'client_position', 
        'client_company',
        'client_image',
        'content', 
        'rating',
        'service_id',
        'is_featured',
        'status',
        'created_at'
    ];

    /**
     * Constructor
     * 
     * @param Database|null $db Database connection
     */
    public function __construct(?Database $db = null)
    {
        parent::__construct();
        if ($db) {
            $this->db = $db;
        }
    }

    /**
     * Get recent testimonials
     * 
     * @param int $limit Number of testimonials to return
     * @return array List of testimonials
     */
    public function getRecent($limit = 4)
    {
        $query = "
            SELECT 
                t.*, 
                s.name AS service_name,
                s.slug AS service_slug
            FROM {$this->table} AS t
            LEFT JOIN services AS s ON t.service_id = s.id
            WHERE t.status = 'approved'
            ORDER BY t.created_at DESC
            LIMIT :limit
        ";
        
        return $this->db->query($query)
            ->bind([':limit' => $limit])
            ->fetchAll();
    }
    
    /**
     * Get featured testimonials
     * 
     * @param int $limit Number of testimonials to return
     * @return array List of testimonials
     */
    public function getFeatured($limit = 4)
    {
        $query = "
            SELECT 
                t.*, 
                s.name AS service_name,
                s.slug AS service_slug
            FROM {$this->table} AS t
            LEFT JOIN services AS s ON t.service_id = s.id
            WHERE t.is_featured = 1 AND t.status = 'approved'
            ORDER BY t.rating DESC, t.created_at DESC
            LIMIT :limit
        ";
        
        return $this->db->query($query)
            ->bind([':limit' => $limit])
            ->fetchAll();
    }
    
    /**
     * Get testimonials by service
     * 
     * @param int $serviceId Service ID
     * @param int $limit Number of testimonials to return
     * @return array List of testimonials
     */
    public function getByService($serviceId, $limit = 10)
    {
        $query = "
            SELECT * FROM {$this->table}
            WHERE service_id = :service_id AND status = 'approved'
            ORDER BY rating DESC, created_at DESC
            LIMIT :limit
        ";
        
        return $this->db->query($query)
            ->bind([
                ':service_id' => $serviceId,
                ':limit' => $limit
            ])
            ->fetchAll();
    }
    
    /**
     * Submit a new testimonial
     * 
     * @param array $data Testimonial data
     * @return int|false Inserted ID or false on failure
     */
    public function submit(array $data)
    {
        // Set default status to pending if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }
        
        // Set creation date if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->create($data);
    }
    
    /**
     * Change testimonial status
     * 
     * @param int $id Testimonial ID
     * @param string $status New status
     * @return bool Success status
     */
    public function changeStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Get testimonial stats
     * 
     * @return array Stats data
     */
    public function getStats()
    {
        $query = "
            SELECT 
                COUNT(*) AS total,
                AVG(rating) AS average_rating,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) AS approved,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) AS rejected,
                COUNT(CASE WHEN rating = 5 THEN 1 END) AS five_star,
                COUNT(CASE WHEN rating = 4 THEN 1 END) AS four_star,
                COUNT(CASE WHEN rating = 3 THEN 1 END) AS three_star,
                COUNT(CASE WHEN rating = 2 THEN 1 END) AS two_star,
                COUNT(CASE WHEN rating = 1 THEN 1 END) AS one_star
            FROM {$this->table}
        ";
        
        return $this->db->query($query)->fetch();
    }
}