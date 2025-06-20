class ConsoleServiceProvider extends ServiceProvider {
    protected $commands = [
        'App\Console\Commands\MigrateCommand',
        'App\Console\Commands\RollbackCommand',
        'App\Console\Commands\SeedCommand',
        'App\Console\Commands\CacheClearCommand',
        'App\Console\Commands\QueueWorkCommand',
        'App\Console\Commands\QueueListenCommand',
        'App\Console\Commands\MakeModelCommand',
        'App\Console\Commands\MakeControllerCommand',
        'App\Console\Commands\MakeMiddlewareCommand',
        'App\Console\Commands\MakeCommandCommand'
    ];

    public function register() {
        $this->singleton('console', function() {
            return new Console($this->container);
        });

        $this->registerCommands();
    }

    protected function registerCommands() {
        foreach ($this->commands as $command) {
            $this->singleton($command, function() use ($command) {
                return new $command($this->container);
            });
        }
    }

    public function boot() {
        $console = $this->container->make('console');

        foreach ($this->commands as $command) {
            $console->add($this->container->make($command));
        }

        $this->registerCustomCommands();
    }

    protected function registerCustomCommands() {
        $commands = Config::getInstance()->get('console.commands', []);
        
        foreach ($commands as $command) {
            if (class_exists($command)) {
                $this->container->make('console')
                    ->add(new $command($this->container));
            }
        }
    }

    protected function schedule() {
        $schedule = $this->container->make('schedule');

        // Define scheduled tasks
        $schedule->command('queue:work')
                ->everyMinute()
                ->withoutOverlapping();

        $schedule->command('backup:run')
                ->daily()
                ->at('01:00');

        $schedule->command('cache:clear')
                ->weekly();

        $schedule->command('log:cleanup')
                ->monthly();
    }}