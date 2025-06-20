<?php
/**
 * Gmail Mailer Service
 * 
 * Handles email sending via Gmail SMTP
 */

namespace App\Services\Mailer;

use App\Core\ConfigManager;
use App\Utilities\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class GmailMailer
{
    /**
     * @var PHPMailer
     */
    protected $mailer;
    
    /**
     * @var ConfigManager
     */
    protected $config;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = ConfigManager::getInstance();
        $this->mailer = new PHPMailer(true);
        
        // Configure Gmail SMTP settings
        $this->setupMailer();
    }
    
    /**
     * Setup mailer with Gmail SMTP configuration
     */
    protected function setupMailer()
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config->get('gmail_username', '');
            $this->mailer->Password = $this->config->get('gmail_app_password', '');
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;
            
            // Default sender
            $fromEmail = $this->config->get('mail_from_address', $this->mailer->Username);
            $fromName = $this->config->get('mail_from_name', 'Gideons Technology Ltd');
            $this->mailer->setFrom($fromEmail, $fromName);
            
            // HTML email by default
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            Logger::error('Mailer setup error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Send an email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email body (HTML)
     * @param string $plainText Plain text version
     * @param array $attachments Optional file attachments
     * @param array $cc CC recipients
     * @param array $bcc BCC recipients
     * @return bool Whether sending was successful
     */
    public function send(
        string $to, 
        string $subject, 
        string $message, 
        string $plainText = '', 
        array $attachments = [], 
        array $cc = [], 
        array $bcc = []
    ): bool {
        try {
            // Reset recipients to avoid duplicates from previous sends
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Set recipients
            $this->mailer->addAddress($to);
            
            // Add CC recipients
            foreach ($cc as $email) {
                $this->mailer->addCC($email);
            }
            
            // Add BCC recipients
            foreach ($bcc as $email) {
                $this->mailer->addBCC($email);
            }
            
            // Set content
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;
            
            // Set plain text version if provided
            if (!empty($plainText)) {
                $this->mailer->AltBody = $plainText;
            }
            
            // Add attachments
            foreach ($attachments as $path => $name) {
                // If numeric key, use the value as the path and original filename
                if (is_numeric($path)) {
                    $this->mailer->addAttachment($name);
                } 
                // If string key, use the key as path and value as new filename
                else {
                    $this->mailer->addAttachment($path, $name);
                }
            }
            
            // Send the email
            $this->mailer->send();
            
            Logger::info('Email sent successfully to: ' . $to, [
                'subject' => $subject
            ]);
            
            return true;
            
        } catch (Exception $e) {
            Logger::error('Email sending failed: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Test the email configuration
     * 
     * @param string $testEmail Email to send test message to
     * @return array Result with success status and message
     */
    public function testConnection(string $testEmail = ''): array
    {
        // If no test email provided, use the configured one
        if (empty($testEmail)) {
            $testEmail = $this->config->get('admin_email', $this->mailer->Username);
        }
        
        try {
            $subject = 'Gideons Technology - Test Email';
            $message = '
                <h1>Email Test</h1>
                <p>This is a test email from your Gideons Technology website.</p>
                <p>If you received this email, your email configuration is working correctly.</p>
                <p>Time sent: ' . date('Y-m-d H:i:s') . '</p>
            ';
            
            $success = $this->send($testEmail, $subject, $message);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Test email sent successfully to ' . $testEmail
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send test email'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Email test failed: ' . $e->getMessage()
            ];
        }
    }
}
