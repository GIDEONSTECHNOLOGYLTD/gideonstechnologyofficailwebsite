<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $config;

    public function __construct(PHPMailer $mailer, array $config) {
        $this->mailer = $mailer;
        $this->config = $config;
        
        // Configure mailer with provided config
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['username'];
        $this->mailer->Password = $this->config['password'];
        $this->mailer->SMTPSecure = $this->config['encryption'];
        $this->mailer->Port = $this->config['port'];
        $this->mailer->setFrom(
            $this->config['from']['address'],
            $this->config['from']['name']
        );
    }

    public function send($to, $subject, $message, $template = 'default') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            
            $html = $this->renderTemplate($template, [
                'subject' => $subject,
                'message' => $message
            ]);
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $html;
            $this->mailer->AltBody = strip_tags($message);

            return $this->mailer->send();
        } catch (Exception $e) {
            throw new \Exception("Email sending failed: " . $e->getMessage());
        }
    }

    private function renderTemplate($template, $data) {
        $templatePath = APP_PATH . "/views/emails/{$template}.php";
        if (!file_exists($templatePath)) {
            throw new \Exception("Email template not found: {$template}");
        }

        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}