class RedisServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('redis', function() {
            return new RedisManager(
                $this->container,
                Config::getInstance()->get('database.redis.client', 'phpredis'),
                $this->parseConnections()
            );
        });

        $this->singleton('redis.connection', function($app) {
            return $app->make('redis')->connection();
        });
    }

    protected function parseConnections() {
        $config = Config::getInstance();
        $connections = $config->get('database.redis', []);

        if (!isset($connections['default'])) {
            $connections['default'] = [
                'host' => '127.0.0.1',
                'port' => 6379,
                'database' => 0,
            ];
        }

        return $connections;
    }

    public function boot() {
        $this->registerCommands();
    }

    protected function registerCommands() {
        if ($this->container->runningInConsole()) {
            $this->commands([
                'redis.clear' => function() {
                    $this->container->make('redis')->flushdb();
                },
                'redis.info' => function() {
                    return $this->container->make('redis')->info();
                }
            ]);
        }
    }

    public function provides() {
        return ['redis', 'redis.connection'];
    }}