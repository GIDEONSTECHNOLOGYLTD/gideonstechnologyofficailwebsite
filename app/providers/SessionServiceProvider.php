class SessionServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('session', function() {
            return new SessionManager($this->container);
        });

        $this->singleton('session.store', function($app) {
            return $app->make('session')->driver();
        });

        $this->registerHandlers();
    }

    protected function registerHandlers() {
        $this->singleton('session.file', function() {
            return new FileSessionHandler(
                $this->container->make('files'),
                APP_PATH . '/storage/sessions',
                Config::getInstance()->get('session.lifetime')
            );
        });

        $this->singleton('session.database', function() {
            return new DatabaseSessionHandler(
                $this->container->make('db'),
                Config::getInstance()->get('session.table'),
                Config::getInstance()->get('session.lifetime')
            );
        });

        $this->singleton('session.redis', function() {
            return new RedisSessionHandler(
                $this->container->make('redis'),
                Config::getInstance()->get('session.lifetime')
            );
        });

        $this->singleton('session.memcached', function() {
            return new MemcachedSessionHandler(
                $this->container->make('memcached'),
                Config::getInstance()->get('session.lifetime')
            );
        });
    }

    public function boot() {
        $this->setupSession();
        $this->registerMiddleware();
    }

    protected function setupSession() {
        $config = Config::getInstance();
        
        if ($config->get('session.secure')) {
            ini_set('session.cookie_secure', 1);
        }

        if ($config->get('session.http_only')) {
            ini_set('session.cookie_httponly', 1);
        }

        if ($path = $config->get('session.path')) {
            ini_set('session.cookie_path', $path);
        }

        if ($domain = $config->get('session.domain')) {
            ini_set('session.cookie_domain', $domain);
        }
    }

    protected function registerMiddleware() {
        $router = $this->container->make('router');
        
        $router->middleware('session', function($request, $next) {
            if (!$request->hasSession()) {
                $request->setSession($this->container->make('session.store'));
            }

            return $next($request);
        });
    }

    public function provides() {
        return ['session', 'session.store'];
    }}