<?php
class User {
    private $conn;
    private $security;
    private $emailService;

    public function __construct($dbConnection, SecurityUtil $security, EmailService $emailService) {
        $this->conn = $dbConnection;
        $this->security = $security;
        $this->emailService = $emailService;
    }

    public function create($username, $email, $password) {
        try {
            if (!$this->security->isValidEmail($email)) {
                throw new Exception("Invalid email format");
            }

            if (!$this->security->validatePassword($password)) {
                throw new Exception("Password does not meet security requirements");
            }

            $this->conn->begin_transaction();

            // Check for existing user
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Username or email already exists");
            }

            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $verificationToken = $this->security->generateSecureToken();

            $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $username, $email, $hashedPassword, $verificationToken);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create user");
            }

            $userId = $this->conn->insert_id;

            // Send verification email
            if (!$this->emailService->sendVerificationEmail($email, $verificationToken)) {
                throw new Exception("Failed to send verification email");
            }

            $this->conn->commit();
            return $userId;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, password, is_verified FROM users WHERE username = ? AND is_active = 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (!$row['is_verified']) {
                throw new Exception("Please verify your email before logging in");
            }

            if (password_verify($password, $row['password'])) {
                $this->updateLastLogin($row['id']);
                return $row['id'];
            }
        }

        return false;
    }

    private function updateLastLogin($userId) {
        $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
    }

    public function requestPasswordReset($email) {
        $token = $this->security->generateSecureToken();
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ? AND is_active = 1");
        $stmt->bind_param('sss', $token, $expiry, $email);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return $this->emailService->sendPasswordResetEmail($email, $token);
        }
        return false;
    }

    public function resetPassword($token, $newPassword) {
        if (!$this->security->validatePassword($newPassword)) {
            throw new Exception("Password does not meet security requirements");
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL 
                                    WHERE reset_token = ? AND reset_token_expires > NOW() AND is_active = 1");
        $stmt->bind_param('ss', $hashedPassword, $token);
        
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}
?>