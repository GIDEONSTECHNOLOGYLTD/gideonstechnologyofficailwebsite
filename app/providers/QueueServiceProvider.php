class QueueServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('queue', function() {
            $config = Config::getInstance()->get('queue');
            
            switch ($config['driver']) {
                case 'redis':
                    return new RedisQueue($config);
                case 'database':
                    return new DatabaseQueue($config);
                case 'sqs':
                    return new SqsQueue($config);
                default:
                    return new SyncQueue();
            }
        });

        $this->singleton('queue.worker', function() {
            return new QueueWorker(
                $this->container->make('queue'),
                $this->container->make('events'),
                $this->container->make('logger')
            );
        });

        $this->singleton('queue.listener', function() {
            return new QueueListener(
                $this->container->make('queue.worker')
            );
        });
    }

    public function boot() {
        $this->registerCommands();
        $this->registerFailedJobs();
    }

    protected function registerCommands() {
        $this->singleton('command.queue.work', function() {
            return new WorkCommand($this->container->make('queue.worker'));
        });

        $this->singleton('command.queue.listen', function() {
            return new ListenCommand($this->container->make('queue.listener'));
        });

        $this->singleton('command.queue.retry', function() {
            return new RetryCommand();
        });

        $this->singleton('command.queue.failed', function() {
            return new ListFailedCommand();
        });

        $this->singleton('command.queue.flush', function() {
            return new FlushFailedCommand();
        });
    }

    protected function registerFailedJobs() {
        $this->singleton('queue.failer', function() {
            $config = Config::getInstance()->get('queue.failed');
            
            switch ($config['driver']) {
                case 'database':
                    return new DatabaseFailedJobProvider($config);
                default:
                    return new NullFailedJobProvider();
            }
        });
    }
}