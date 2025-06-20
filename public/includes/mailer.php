<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

class Mailer {
    private $mailer;
    private $config;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->config = require __DIR__ . '/../../app/config/mail.php';
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['username'];
        $this->mailer->Password = $this->config['password'];
        $this->mailer->SMTPSecure = $this->config['encryption'];
        $this->mailer->Port = $this->config['port'];
        
        // Default settings
        $this->mailer->isHTML(true);
        $this->mailer->setFrom(
            $this->config['from']['address'],
            $this->config['from']['name']
        );
    }
    
    public function sendContactForm($data) {
        try {
            // Add admin recipient
            $this->mailer->addAddress($this->config['reply_to']['address']);
            
            // Set subject based on service if selected
            $subject = 'New Contact Form Submission';
            if (!empty($data['service'])) {
                $subject .= ' - ' . $data['service'];
            }
            $this->mailer->Subject = $subject;
            
            // Add attachment if present
            if (!empty($data['attachment'])) {
                $this->mailer->addAttachment($data['attachment']);
            }
            
            // Set reply-to as the contact form submitter
            if (!empty($data['email'])) {
                $this->mailer->addReplyTo($data['email'], $data['name']);
            }
            
            $message = $this->getContactFormTemplate($data);
            $this->mailer->Body = $message;
            $this->mailer->AltBody = strip_tags($message);
            
            $success = $this->mailer->send();
            
            // Clear all addresses and attachments for next use
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            return $success;
        } catch (Exception $e) {
            error_log("Mailer Error (Contact Form): " . $e->getMessage());
            return false;
        }
    }
    
    public function sendPasswordReset($email, $resetLink) {
        try {
            $this->mailer->addAddress($email);
            $this->mailer->Subject = 'Password Reset Request';
            
            $message = $this->getPasswordResetTemplate($resetLink);
            $this->mailer->Body = $message;
            $this->mailer->AltBody = strip_tags($message);
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Mailer Error (Password Reset): " . $e->getMessage());
            return false;
        }
    }
    
    private function getContactFormTemplate($data) {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h2 style="color: #007bff;">New Contact Form Submission</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Name:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($data['name']) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Email:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($data['email']) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Phone:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($data['phone']) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Service:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($data['service']) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;"><strong>Message:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">' . nl2br(htmlspecialchars($data['message'])) . '</td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
    }
    
    private function getPasswordResetTemplate($resetLink) {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h2 style="color: #007bff;">Password Reset Request</h2>
                <p>You have requested to reset your password. Click the button below to proceed:</p>
                <p style="text-align: center; margin: 30px 0;">
                    <a href="' . htmlspecialchars($resetLink) . '" 
                       style="background-color: #007bff; color: white; padding: 12px 24px; 
                              text-decoration: none; border-radius: 4px;">
                        Reset Password
                    </a>
                </p>
                <p>This link will expire in 1 hour for security reasons.</p>
                <p>If you did not request this password reset, please ignore this email.</p>
                <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                <p style="color: #666; font-size: 12px;">
                    This is an automated email from ' . SITE_NAME . '. Please do not reply to this email.
                </p>
            </div>
        </body>
        </html>';
    }
}
