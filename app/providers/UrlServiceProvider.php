class UrlServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('url', function() {
            return new UrlGenerator(
                $this->container->make('request'),
                Config::getInstance()->get('app.url')
            );
        });

        $this->registerGenerators();
    }

    protected function registerGenerators() {
        $this->singleton('url.assets', function() {
            return new AssetUrlGenerator(
                Config::getInstance()->get('app.asset_url')
            );
        });

        $this->singleton('url.route', function() {
            return new RouteUrlGenerator(
                $this->container->make('router'),
                $this->container->make('request')
            );
        });

        $this->singleton('url.signedRoute', function() {
            return new SignedUrlGenerator(
                $this->container->make('url'),
                Config::getInstance()->get('app.key')
            );
        });
    }

    public function boot() {
        $this->registerMacros();
        $this->configureForceScheme();
    }

    protected function registerMacros() {
        $url = $this->container->make('url');

        $url->macro('signedRoute', function($name, $parameters = [], $expiration = null) {
            return $this->container->make('url.signedRoute')
                ->create($name, $parameters, $expiration);
        });

        $url->macro('temporarySignedRoute', function($name, $expiration, $parameters = []) {
            return $this->container->make('url.signedRoute')
                ->createTemporary($name, $expiration, $parameters);
        });
    }

    protected function configureForceScheme() {
        $config = Config::getInstance();
        
        if ($config->get('app.force_https')) {
            $this->container->make('url')->forceScheme('https');
        }
    }

    protected function registerHelpers() {
        if (!function_exists('url')) {
            function url($path = null, $parameters = [], $secure = null) {
                return app('url')->to($path, $parameters, $secure);
            }
        }

        if (!function_exists('asset')) {
            function asset($path, $secure = null) {
                return app('url.assets')->asset($path, $secure);
            }
        }

        if (!function_exists('route')) {
            function route($name, $parameters = [], $absolute = true) {
                return app('url.route')->route($name, $parameters, $absolute);
            }
        }
    }}