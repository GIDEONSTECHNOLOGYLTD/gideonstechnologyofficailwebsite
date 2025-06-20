<?php
class Encryption {
    private $key;
    private $cipher;
    private $ivLength;

    public function __construct($key = null) {
        $this->cipher = 'AES-256-CBC';
        $this->key = $key ?? ENCRYPTION_KEY;
        $this->ivLength = openssl_cipher_iv_length($this->cipher);
    }

    public function encrypt($data) {
        $iv = random_bytes($this->ivLength);
        
        $encrypted = openssl_encrypt(
            serialize($data),
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encrypted === false) {
            throw new Exception('Encryption failed');
        }

        $hmac = hash_hmac('sha256', $encrypted, $this->key, true);
        
        return base64_encode($iv . $hmac . $encrypted);
    }

    public function decrypt($data) {
        $data = base64_decode($data);
        
        $iv = substr($data, 0, $this->ivLength);
        $hmac = substr($data, $this->ivLength, 32);
        $encrypted = substr($data, $this->ivLength + 32);
        
        $calculatedHmac = hash_hmac('sha256', $encrypted, $this->key, true);
        
        if (!hash_equals($hmac, $calculatedHmac)) {
            throw new Exception('Message authentication failed');
        }

        $decrypted = openssl_decrypt(
            $encrypted,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            throw new Exception('Decryption failed');
        }

        return unserialize($decrypted);
    }

    public function generateKey() {
        return bin2hex(random_bytes(32));
    }

    public function hash($data) {
        return password_hash($data, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verify($data, $hash) {
        return password_verify($data, $hash);
    }

    public function encryptFile($sourcePath, $destinationPath) {
        $iv = random_bytes($this->ivLength);
        $key = random_bytes(32);
        
        $fpSource = fopen($sourcePath, 'rb');
        $fpDest = fopen($destinationPath, 'wb');
        
        // Write IV and encrypted key
        fwrite($fpDest, $iv);
        fwrite($fpDest, openssl_encrypt($key, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv));
        
        // Encrypt file contents
        while (!feof($fpSource)) {
            $plaintext = fread($fpSource, 16 * 1024); // 16KB chunks
            $ciphertext = openssl_encrypt(
                $plaintext,
                $this->cipher,
                $key,
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $iv
            );
            fwrite($fpDest, $ciphertext);
            $iv = substr($ciphertext, 0, $this->ivLength);
        }
        
        fclose($fpSource);
        fclose($fpDest);
        
        return true;
    }

    public function decryptFile($sourcePath, $destinationPath) {
        $fpSource = fopen($sourcePath, 'rb');
        $fpDest = fopen($destinationPath, 'wb');
        
        // Read IV and encrypted key
        $iv = fread($fpSource, $this->ivLength);
        $encryptedKey = fread($fpSource, 48); // Encrypted key length
        $key = openssl_decrypt($encryptedKey, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        
        // Decrypt file contents
        while (!feof($fpSource)) {
            $ciphertext = fread($fpSource, 16 * 1024 + 16); // 16KB chunks + block size
            $plaintext = openssl_decrypt(
                $ciphertext,
                $this->cipher,
                $key,
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $iv
            );
            fwrite($fpDest, $plaintext);
            $iv = substr($ciphertext, 0, $this->ivLength);
        }
        
        fclose($fpSource);
        fclose($fpDest);
        
        return true;
    }
}