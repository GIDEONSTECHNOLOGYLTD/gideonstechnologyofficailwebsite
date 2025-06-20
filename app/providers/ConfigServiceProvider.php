class ConfigServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('config', function() {
            return new ConfigRepository();
        });

        $this->loadConfigurations();
    }

    protected function loadConfigurations() {
        $files = glob(APP_PATH . '/config/*.php');
        $config = $this->container->make('config');

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $config->set($name, require $file);
        }

        if (file_exists($custom = APP_PATH . '/config/custom.php')) {
            $config->set('custom', require $custom);
        }

        $this->loadEnvironmentConfig();
    }

    protected function loadEnvironmentConfig() {
        $env = $this->container->environment();
        $config = $this->container->make('config');

        if (file_exists($envConfig = APP_PATH . "/config/{$env}.php")) {
            $values = require $envConfig;
            foreach ($values as $key => $value) {
                $config->set($key, array_merge(
                    $config->get($key, []),
                    $value
                ));
            }
        }
    }

    public function boot() {
        $this->publishConfig();
    }

    protected function publishConfig() {
        if (!is_dir($directory = APP_PATH . '/config')) {
            mkdir($directory, 0755, true);
        }

        $this->publishFile('app.php');
        $this->publishFile('auth.php');
        $this->publishFile('cache.php');
        $this->publishFile('database.php');
        $this->publishFile('mail.php');
        $this->publishFile('queue.php');
        $this->publishFile('services.php');
        $this->publishFile('session.php');
    }

    protected function publishFile($name) {
        $source = __DIR__ . "/../config/{$name}";
        $target = APP_PATH . "/config/{$name}";

        if (!file_exists($target) && file_exists($source)) {
            copy($source, $target);
        }
    }

    public function provides() {
        return ['config'];
    }}