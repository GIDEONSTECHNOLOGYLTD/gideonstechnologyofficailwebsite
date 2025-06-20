class MailServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('mailer', function() {
            return new MailManager($this->container);
        });

        $this->singleton('mailer.driver', function($app) {
            return $app->make('mailer')->driver();
        });

        $this->registerDrivers();
    }

    protected function registerDrivers() {
        $this->singleton('mail.smtp', function() {
            return new SmtpTransport(
                Config::getInstance()->get('mail.smtp')
            );
        });

        $this->singleton('mail.sendmail', function() {
            return new SendmailTransport(
                Config::getInstance()->get('mail.sendmail')
            );
        });

        $this->singleton('mail.mailgun', function() {
            return new MailgunTransport(
                Config::getInstance()->get('services.mailgun')
            );
        });

        $this->singleton('mail.ses', function() {
            return new SesTransport(
                Config::getInstance()->get('services.ses')
            );
        });

        $this->singleton('mail.postmark', function() {
            return new PostmarkTransport(
                Config::getInstance()->get('services.postmark')
            );
        });
    }

    public function boot() {
        $this->registerViews();
        $this->registerEvents();
    }

    protected function registerViews() {
        $this->loadViewsFrom(
            APP_PATH . '/resources/views/mail',
            'mail'
        );
    }

    protected function registerEvents() {
        $events = $this->container->make('events');

        $events->listen('mail.sending', function($message) {
            $this->container->make('logger')->info('Sending email', [
                'to' => $message->getTo(),
                'subject' => $message->getSubject()
            ]);
        });

        $events->listen('mail.sent', function($message) {
            $this->container->make('logger')->info('Email sent', [
                'to' => $message->getTo(),
                'subject' => $message->getSubject()
            ]);
        });

        $events->listen('mail.failed', function($message, $error) {
            $this->container->make('logger')->error('Email failed', [
                'to' => $message->getTo(),
                'subject' => $message->getSubject(),
                'error' => $error
            ]);
        });
    }

    public function provides() {
        return ['mailer', 'mailer.driver'];
    }
}