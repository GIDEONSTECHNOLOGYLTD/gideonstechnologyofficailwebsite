class CacheServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('cache', function() {
            return new CacheManager($this->container);
        });

        $this->singleton('cache.store', function($app) {
            return $app->make('cache')->driver();
        });

        $this->registerDrivers();
    }

    protected function registerDrivers() {
        $this->singleton('cache.file', function() {
            return new FileStore(
                $this->container->make('files'),
                APP_PATH . '/storage/cache'
            );
        });

        $this->singleton('cache.redis', function() {
            return new RedisStore(
                $this->container->make('redis'),
                Config::getInstance()->get('cache.prefix')
            );
        });

        $this->singleton('cache.memcached', function() {
            return new MemcachedStore(
                $this->container->make('memcached'),
                Config::getInstance()->get('cache.prefix')
            );
        });

        $this->singleton('cache.array', function() {
            return new ArrayStore();
        });

        $this->singleton('cache.null', function() {
            return new NullStore();
        });
    }

    public function boot() {
        $this->registerMacros();
        $this->setupTags();
    }

    protected function registerMacros() {
        $cache = $this->container->make('cache');

        $cache->macro('remember', function($key, $ttl, $callback) {
            $value = $this->get($key);

            if (!is_null($value)) {
                return $value;
            }

            $value = $callback();
            $this->put($key, $value, $ttl);

            return $value;
        });

        $cache->macro('rememberForever', function($key, $callback) {
            return $this->remember($key, null, $callback);
        });
    }

    protected function setupTags() {
        if (Config::getInstance()->get('cache.tags', false)) {
            $this->container->make('cache')->setEventDispatcher(
                $this->container->make('events')
            );
        }
    }

    public function provides() {
        return ['cache', 'cache.store'];
    }
}