class Mailer {
    private $mailer;
    private $config;
    private $logger;
    private $templates = [];

    public function __construct() {
        $this->config = Config::getInstance();
        $this->logger = Logger::getInstance();
        $this->initializeMailer();
    }

    private function initializeMailer() {
        $transport = (new \Swift_SmtpTransport(
            $this->config->get('mail.host'),
            $this->config->get('mail.port'),
            $this->config->get('mail.encryption')
        ))
        ->setUsername($this->config->get('mail.username'))
        ->setPassword($this->config->get('mail.password'));

        $this->mailer = new \Swift_Mailer($transport);
    }

    public function send($to, $subject, $template, $data = []) {
        try {
            $message = $this->createMessage($to, $subject, $template, $data);
            $result = $this->mailer->send($message);
            
            $this->logger->info("Email sent successfully", [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error("Failed to send email", [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ]);
            throw $e;
        }
    }

    private function createMessage($to, $subject, $template, $data) {
        $message = new \Swift_Message();
        
        $message->setSubject($subject)
                ->setFrom([
                    $this->config->get('mail.from.address') => 
                    $this->config->get('mail.from.name')
                ])
                ->setTo($to)
                ->setBody(
                    $this->renderTemplate($template, $data),
                    'text/html'
                );

        if ($this->config->get('mail.reply_to')) {
            $message->setReplyTo($this->config->get('mail.reply_to'));
        }

        return $message;
    }

    private function renderTemplate($template, $data) {
        if (!isset($this->templates[$template])) {
            $path = APP_PATH . "/views/emails/{$template}.php";
            
            if (!file_exists($path)) {
                throw new \Exception("Email template not found: {$template}");
            }
            
            $this->templates[$template] = file_get_contents($path);
        }

        return $this->replacePlaceholders(
            $this->templates[$template],
            $data
        );
    }

    private function replacePlaceholders($content, $data) {
        foreach ($data as $key => $value) {
            $content = str_replace(
                ['{{' . $key . '}}', '{{ ' . $key . ' }}'],
                $value,
                $content
            );
        }
        return $content;
    }

    public function queue($to, $subject, $template, $data = []) {
        $job = [
            'to' => $to,
            'subject' => $subject,
            'template' => $template,
            'data' => $data
        ];

        $queue = new Queue();
        return $queue->push('App\\Jobs\\SendEmail', $job);
    }

    public function sendBatch(array $recipients, $subject, $template, $data = []) {
        try {
            $messages = [];
            foreach ($recipients as $to) {
                $messages[] = $this->createMessage($to, $subject, $template, $data);
            }
            
            $result = $this->mailer->send($messages);
            
            $this->logger->info("Batch email sent successfully", [
                'recipients' => count($recipients),
                'subject' => $subject,
                'template' => $template
            ]);
            
            return $result;
        } catch (\Exception $e) {
            $this->logger->error("Failed to send batch email", [
                'error' => $e->getMessage(),
                'recipients' => count($recipients),
                'subject' => $subject
            ]);
            throw $e;
        }
    }

    public function addAttachment($message, $file, $name = null) {
        if (!file_exists($file)) {
            throw new \Exception("Attachment file not found: {$file}");
        }

        $attachment = \Swift_Attachment::fromPath($file);
        if ($name) {
            $attachment->setFilename($name);
        }

        $message->attach($attachment);
        return $message;
    }}