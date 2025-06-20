class TokenService {
    private $db;
    private const TOKEN_LENGTH = 64;
    private const DEFAULT_EXPIRY = 3600; // 1 hour

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function generateToken($userId, $type = 'access', $expiry = null) {
        try {
            $token = bin2hex(random_bytes(self::TOKEN_LENGTH / 2));
            $expiresAt = date('Y-m-d H:i:s', time() + ($expiry ?? self::DEFAULT_EXPIRY));

            $stmt = $this->db->prepare("
                INSERT INTO tokens (
                    user_id, token, type, expires_at, created_at
                ) VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([$userId, $token, $type, $expiresAt]);
            return $token;
        } catch (\Exception $e) {
            throw new \Exception("Failed to generate token: " . $e->getMessage());
        }
    }

    public function validateToken($token, $type = 'access') {
        try {
            $stmt = $this->db->prepare("
                SELECT user_id, expires_at, is_revoked 
                FROM tokens 
                WHERE token = ? AND type = ?
            ");
            
            $stmt->execute([$token, $type]);
            $result = $stmt->fetch();

            if (!$result) {
                return false;
            }

            if ($result['is_revoked']) {
                return false;
            }

            if (strtotime($result['expires_at']) < time()) {
                $this->revokeToken($token);
                return false;
            }

            return $result['user_id'];
        } catch (\Exception $e) {
            throw new \Exception("Token validation failed: " . $e->getMessage());
        }
    }

    public function revokeToken($token) {
        try {
            $stmt = $this->db->prepare("
                UPDATE tokens 
                SET is_revoked = 1, 
                    revoked_at = NOW() 
                WHERE token = ?
            ");
            return $stmt->execute([$token]);
        } catch (\Exception $e) {
            throw new \Exception("Failed to revoke token: " . $e->getMessage());
        }
    }

    public function revokeAllUserTokens($userId, $type = null) {
        try {
            $sql = "
                UPDATE tokens 
                SET is_revoked = 1, 
                    revoked_at = NOW() 
                WHERE user_id = ?
            ";
            $params = [$userId];

            if ($type) {
                $sql .= " AND type = ?";
                $params[] = $type;
            }

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) {
            throw new \Exception("Failed to revoke user tokens: " . $e->getMessage());
        }
    }

    public function refreshToken($oldToken) {
        try {
            $this->db->beginTransaction();

            $userId = $this->validateToken($oldToken, 'refresh');
            if (!$userId) {
                throw new \Exception("Invalid refresh token");
            }

            $this->revokeToken($oldToken);
            $newToken = $this->generateToken($userId);

            $this->db->commit();
            return $newToken;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Token refresh failed: " . $e->getMessage());
        }
    }

    public function cleanupExpiredTokens() {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM tokens 
                WHERE expires_at < NOW() 
                OR (is_revoked = 1 AND revoked_at < DATE_SUB(NOW(), INTERVAL 30 DAY))
            ");
            return $stmt->execute();
        } catch (\Exception $e) {
            throw new \Exception("Token cleanup failed: " . $e->getMessage());
        }
    }