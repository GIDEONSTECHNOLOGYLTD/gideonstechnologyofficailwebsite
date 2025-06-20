<?php
namespace App\Services;

class TwoFactorAuth {
    private $secretLength = 16;
    private $pinModulo = 1000000;
    private $pinLength = 6;
    private $pinTimeout = 30;

    public function generateSecret() {
        $secret = random_bytes($this->secretLength);
        return base32_encode($secret);
    }

    public function getQRCodeUrl($email) {
        $secret = $this->generateSecret();
        $appName = urlencode('Gideons Technology');
        $email = urlencode($email);
        return "otpauth://totp/{$appName}:{$email}?secret={$secret}&issuer={$appName}";
    }

    public function verifyCode($secret, $code) {
        if (strlen($code) !== $this->pinLength) {
            return false;
        }

        $timeSlice = floor(time() / $this->pinTimeout);
        
        // Check current time slice and previous one
        for ($i = -1; $i <= 1; $i++) {
            if ($this->generateCode($secret, $timeSlice + $i) === $code) {
                return true;
            }
        }

        return false;
    }

    private function generateCode($secret, $timeSlice) {
        $secretkey = base32_decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hm = hash_hmac('SHA1', $time, $secretkey, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashpart = substr($hm, $offset, 4);
        $value = unpack('N', $hashpart)[1] & 0x7FFFFFFF;
        return str_pad($value % $this->pinModulo, $this->pinLength, '0', STR_PAD_LEFT);
    }
}

function base32_encode($data) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $padding = '=';
    $binary = '';
    $dataSize = strlen($data);
    
    // Convert string to binary
    for ($i = 0; $i < $dataSize; $i++) {
        $binary .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
    }
    
    // Pad binary string
    $binaryLength = strlen($binary);
    $paddingLength = (8 - ($binaryLength % 8)) % 8;
    $binary .= str_repeat('0', $paddingLength);
    
    // Convert binary to base32
    $base32 = '';
    for ($i = 0; $i < strlen($binary); $i += 5) {
        $chunk = substr($binary, $i, 5);
        $base32 .= $alphabet[bindec($chunk)];
    }
    
    return $base32;
}

function base32_decode($data) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $binary = '';
    
    // Convert base32 to binary
    $dataSize = strlen($data);
    for ($i = 0; $i < $dataSize; $i++) {
        $position = strpos($alphabet, $data[$i]);
        $binary .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
    }
    
    // Convert binary to string
    $result = '';
    $binaryLength = strlen($binary);
    for ($i = 0; $i + 8 <= $binaryLength; $i += 8) {
        $chunk = substr($binary, $i, 8);
        $result .= chr(bindec($chunk));
    }
    
    return $result;
}