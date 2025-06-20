class LoggerServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('logger', function() {
            $config = Config::getInstance()->get('logging');
            return new LogManager($config);
        });

        $this->registerHandlers();
        $this->registerProcessors();
    }

    protected function registerHandlers() {
        $this->singleton('logger.file', function() {
            return new FileHandler(
                APP_PATH . '/storage/logs/app.log',
                Config::getInstance()->get('logging.level')
            );
        });

        $this->singleton('logger.daily', function() {
            return new RotatingFileHandler(
                APP_PATH . '/storage/logs/app.log',
                Config::getInstance()->get('logging.days', 7)
            );
        });

        $this->singleton('logger.slack', function() {
            $config = Config::getInstance()->get('logging.slack');
            return new SlackHandler(
                $config['webhook_url'],
                $config['channel']
            );
        });

        $this->singleton('logger.syslog', function() {
            return new SyslogHandler('application');
        });
    }

    protected function registerProcessors() {
        $this->singleton('logger.processor.web', function() {
            return new WebProcessor();
        });

        $this->singleton('logger.processor.introspection', function() {
            return new IntrospectionProcessor();
        });

        $this->singleton('logger.processor.memory', function() {
            return new MemoryUsageProcessor();
        });

        $this->singleton('logger.processor.git', function() {
            return new GitProcessor();
        });
    }

    public function boot() {
        $logger = $this->container->make('logger');
        
        // Configure default channel
        $logger->useFiles(
            APP_PATH . '/storage/logs/app.log',
            Config::getInstance()->get('logging.level')
        );

        // Add processors
        $logger->pushProcessor(
            $this->container->make('logger.processor.web')
        );
        
        $logger->pushProcessor(
            $this->container->make('logger.processor.memory')
        );

        // Configure error handler
        $logger->configureErrorHandler();
    }

    protected function configureChannels() {
        $channels = Config::getInstance()->get('logging.channels', []);
        
        foreach ($channels as $name => $config) {
            $this->configureChannel($name, $config);
        }
    }

    protected function configureChannel($name, array $config) {
        $logger = $this->container->make('logger');
        
        switch ($config['driver']) {
            case 'file':
                $logger->useFiles(
                    $config['path'],
                    $config['level'] ?? 'debug'
                );
                break;
                
            case 'daily':
                $logger->useDailyFiles(
                    $config['path'],
                    $config['days'] ?? 7,
                    $config['level'] ?? 'debug'
                );
                break;
                
            case 'slack':
                $logger->useSlack(
                    $config['url'],
                    $config['channel'] ?? null,
                    $config['username'] ?? 'Logger',
                    $config['emoji'] ?? ':boom:',
                    $config['level'] ?? 'critical'
                );
                break;
        }
    }}