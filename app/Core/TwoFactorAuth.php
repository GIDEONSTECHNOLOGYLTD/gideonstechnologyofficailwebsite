<?php
namespace App\Core;

class TwoFactorAuth {
    private const ALGORITHM = 'sha1';
    private const DIGITS = 6;
    private const PERIOD = 30;
    private const SECRET_LENGTH = 32;

    public function generateSecret() {
        $secret = random_bytes(self::SECRET_LENGTH);
        return base32_encode($secret);
    }

    public function getQRCodeUrl($userIdentifier) {
        $otpParams = [
            'secret' => $this->getSecret(),
            'issuer' => 'YourApp',
            'algorithm' => self::ALGORITHM,
            'digits' => self::DIGITS,
            'period' => self::PERIOD
        ];
        
        $query = http_build_query($otpParams);
        return "otpauth://totp/{$userIdentifier}?{$query}";
    }

    public function verifyCode($secret, $code) {
        $timeSlice = floor(time() / self::PERIOD);
        
        // Check current time slice and adjacent ones
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = $this->generateCode($secret, $timeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }

    private function generateCode($secret, $timeSlice) {
        $secretKey = base32_decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac(self::ALGORITHM, $time, $secretKey, true);
        $offset = ord($hmac[strlen($hmac) - 1]) & 0x0F;
        $code = (
            ((ord($hmac[$offset + 0]) & 0x7F) << 24) |
            ((ord($hmac[$offset + 1]) & 0xFF) << 16) |
            ((ord($hmac[$offset + 2]) & 0xFF) << 8) |
            (ord($hmac[$offset + 3]) & 0xFF)
        ) % pow(10, self::DIGITS);
        
        return str_pad($code, self::DIGITS, '0', STR_PAD_LEFT);
    }
}