<?php
namespace App\Services;

class UserService {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateUserAvatar($userId, $avatarPath) {
        try {
            // Validate avatar path
            if ($avatarPath !== null && !file_exists($avatarPath)) {
                throw new \RuntimeException('Avatar file does not exist');
            }
            
            $stmt = $this->pdo->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$avatarPath, $userId]);
        } catch (\PDOException $e) {
            error_log('Error updating user avatar: ' . $e->getMessage());
            return false;
        }
    }

    public function updateUserName($userId, $name) {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $userId]);
    }

    public function updateUserPassword($userId, $hashedPassword) {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
}