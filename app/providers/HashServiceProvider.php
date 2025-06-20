class HashServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('hash', function() {
            return new HashManager($this->container);
        });

        $this->singleton('hash.driver', function($app) {
            return $app->make('hash')->driver();
        });

        $this->registerDrivers();
    }

    protected function registerDrivers() {
        $this->singleton('hash.bcrypt', function() {
            return new BcryptHasher([
                'rounds' => Config::getInstance()->get('hashing.bcrypt.rounds', 10)
            ]);
        });

        $this->singleton('hash.argon', function() {
            return new ArgonHasher([
                'memory' => Config::getInstance()->get('hashing.argon.memory'),
                'threads' => Config::getInstance()->get('hashing.argon.threads'),
                'time' => Config::getInstance()->get('hashing.argon.time')
            ]);
        });

        $this->singleton('hash.argon2id', function() {
            return new Argon2IdHasher([
                'memory' => Config::getInstance()->get('hashing.argon.memory'),
                'threads' => Config::getInstance()->get('hashing.argon.threads'),
                'time' => Config::getInstance()->get('hashing.argon.time')
            ]);
        });
    }

    public function boot() {
        $this->registerMacros();
    }

    protected function registerMacros() {
        $hash = $this->container->make('hash');

        $hash->macro('check', function($value, $hashedValue) {
            return $this->driver()->check($value, $hashedValue);
        });

        $hash->macro('needsRehash', function($hashedValue) {
            return $this->driver()->needsRehash($hashedValue);
        });
    }

    public function provides() {
        return ['hash', 'hash.driver'];
    }}