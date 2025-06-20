<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    private $template;
    private $queue;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->template = new Template();
        $this->queue = new Queue();
        $this->configure();
    }

    private function configure() {
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USER;
        $this->mailer->Password = SMTP_PASS;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = SMTP_PORT;
        $this->mailer->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $this->mailer->isHTML(true);
    }

    public function send($to, $subject, $template, $data = [], $attachments = [], $queue = true) {
        if ($queue) {
            return $this->queue->push('sendEmail', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'data' => $data,
                'attachments' => $attachments
            ], 'emails');
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            if (is_array($to)) {
                foreach ($to as $address) {
                    $this->mailer->addAddress($address);
                }
            } else {
                $this->mailer->addAddress($to);
            }

            $this->mailer->Subject = $subject;
            $this->mailer->Body = $this->template->render("emails/{$template}", $data);
            $this->mailer->AltBody = strip_tags($this->mailer->Body);

            foreach ($attachments as $attachment) {
                if (is_array($attachment)) {
                    $this->mailer->addAttachment($attachment['path'], $attachment['name']);
                } else {
                    $this->mailer->addAttachment($attachment);
                }
            }

            return $this->mailer->send();

        } catch (Exception $e) {
            Logger::getInstance()->error("Email sending failed: {$e->getMessage()}", [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            throw $e;
        }
    }

    public function sendWelcomeEmail($user) {
        return $this->send(
            $user['email'],
            'Welcome to ' . APP_NAME,
            'welcome',
            ['user' => $user]
        );
    }

    public function sendPasswordReset($user, $token) {
        return $this->send(
            $user['email'],
            'Password Reset Request',
            'password-reset',
            [
                'user' => $user,
                'token' => $token,
                'expires' => date('Y-m-d H:i:s', time() + TOKEN_EXPIRY)
            ]
        );
    }

    public function sendOrderConfirmation($order, $user) {
        return $this->send(
            $user['email'],
            'Order Confirmation #' . $order['id'],
            'order-confirmation',
            [
                'order' => $order,
                'user' => $user
            ]
        );
    }

    public function sendNotification($email, $type, $message, $data = []) {
        return $this->send(
            $email,
            ucfirst($type) . ' Notification',
            'notification',
            [
                'type' => $type,
                'message' => $message,
                'data' => $data
            ]
        );
    }

    public function sendContactForm($data) {
        return $this->send(
            EMAIL_FROM,
            'New Contact Form Submission',
            'contact-form',
            ['data' => $data],
            [],
            false // Send immediately
        );
    }

    public function sendBulk($recipients, $subject, $template, $data = []) {
        foreach (array_chunk($recipients, 50) as $chunk) {
            $this->queue->push('sendBulkEmail', [
                'recipients' => $chunk,
                'subject' => $subject,
                'template' => $template,
                'data' => $data
            ], 'emails');
        }
    }

    public function getMailer() {
        return $this->mailer;
    }
}