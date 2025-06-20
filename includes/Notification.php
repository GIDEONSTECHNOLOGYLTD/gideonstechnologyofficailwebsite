<?php
class Notification {
    private $db;
    private $user_id;
    private $channels = ['database', 'email', 'sms'];

    public function __construct($user_id = null) {
        $this->db = Database::getInstance();
        $this->user_id = $user_id;
    }

    public function send($type, $message, $data = [], $channels = ['database']) {
        foreach ($channels as $channel) {
            $method = 'sendVia' . ucfirst($channel);
            if (method_exists($this, $method)) {
                $this->$method($type, $message, $data);
            }
        }
    }

    private function sendViaDatabase($type, $message, $data) {
        $stmt = $this->db->prepare(
            "INSERT INTO notifications (user_id, type, message, data, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        $jsonData = json_encode($data);
        $stmt->bind_param('isss', $this->user_id, $type, $message, $jsonData);
        return $stmt->execute();
    }

    private function sendViaEmail($type, $message, $data) {
        $stmt = $this->db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param('i', $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            $emailService = new EmailService();
            $emailService->sendNotification($result['email'], $type, $message, $data);
        }
    }

    private function sendViaSms($type, $message, $data) {
        // SMS implementation would go here
        // Left as placeholder for future implementation
    }

    public function getUnread($limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications 
             WHERE user_id = ? AND read_at IS NULL 
             ORDER BY created_at DESC 
             LIMIT ?"
        );
        $stmt->bind_param('ii', $this->user_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function markAsRead($notification_id) {
        $stmt = $this->db->prepare(
            "UPDATE notifications 
             SET read_at = NOW() 
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param('ii', $notification_id, $this->user_id);
        return $stmt->execute();
    }

    public function markAllAsRead() {
        $stmt = $this->db->prepare(
            "UPDATE notifications 
             SET read_at = NOW() 
             WHERE user_id = ? AND read_at IS NULL"
        );
        $stmt->bind_param('i', $this->user_id);
        return $stmt->execute();
    }

    public function delete($notification_id) {
        $stmt = $this->db->prepare(
            "DELETE FROM notifications 
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param('ii', $notification_id, $this->user_id);
        return $stmt->execute();
    }

    public function deleteAll() {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->bind_param('i', $this->user_id);
        return $stmt->execute();
    }

    public function getNotificationCount() {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count 
             FROM notifications 
             WHERE user_id = ? AND read_at IS NULL"
        );
        $stmt->bind_param('i', $this->user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['count'];
    }

    public function getAll($page = 1, $perPage = DEFAULT_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare(
            "SELECT SQL_CALC_FOUND_ROWS * 
             FROM notifications 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?, ?"
        );
        $stmt->bind_param('iii', $this->user_id, $offset, $perPage);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $total = $this->db->query("SELECT FOUND_ROWS() as total")
                         ->fetch_assoc()['total'];
        
        return [
            'notifications' => $results,
            'pagination' => (new Paginator($total, $page, $perPage))->toArray()
        ];
    }
}