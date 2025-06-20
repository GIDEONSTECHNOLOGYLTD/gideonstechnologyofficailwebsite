<?php
class EmailService {
    private $fromEmail = 'noreply@gideonstechnology.com';
    private $fromName = 'Gideons Technology';

    public function sendVerificationEmail($toEmail, $token) {
        $subject = 'Verify Your Email - Gideons Technology';
        $verifyUrl = "https://gideonstechnology.com/verify.php?token=" . urlencode($token);
        
        $message = $this->getEmailTemplate('verification', [
            'verifyUrl' => $verifyUrl,
            'supportEmail' => 'support@gideonstechnology.com'
        ]);

        return $this->sendEmail($toEmail, $subject, $message);
    }

    public function sendPasswordResetEmail($toEmail, $token) {
        $subject = 'Password Reset Request - Gideons Technology';
        $resetUrl = "https://gideonstechnology.com/reset-password.php?token=" . urlencode($token);
        
        $message = $this->getEmailTemplate('password_reset', [
            'resetUrl' => $resetUrl,
            'supportEmail' => 'support@gideonstechnology.com'
        ]);

        return $this->sendEmail($toEmail, $subject, $message);
    }

    private function sendEmail($to, $subject, $message) {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        try {
            return mail($to, $subject, $message, implode("\r\n", $headers));
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    private function getEmailTemplate($type, $vars) {
        $template = file_get_contents(__DIR__ . "/../templates/email/{$type}.html");
        foreach ($vars as $key => $value) {
            $template = str_replace("{{" . $key . "}}", $value, $template);
        }
        return $template;
    }
}
?>