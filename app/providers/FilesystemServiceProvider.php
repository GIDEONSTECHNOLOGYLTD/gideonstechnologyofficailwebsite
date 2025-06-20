class FilesystemServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('files', function() {
            return new Filesystem();
        });

        $this->singleton('filesystem', function() {
            return new FilesystemManager($this->container);
        });

        $this->registerDisks();
    }

    protected function registerDisks() {
        $config = Config::getInstance();
        $manager = $this->container->make('filesystem');

        foreach ($config->get('filesystems.disks', []) as $name => $disk) {
            $manager->extend($name, function() use ($disk) {
                return $this->createDriver($disk);
            });
        }
    }

    protected function createDriver($config) {
        switch ($config['driver']) {
            case 'local':
                return new LocalFilesystemAdapter(
                    $config['root'],
                    $config['permissions'] ?? [],
                    $config['links'] ?? LocalFilesystemAdapter::DISALLOW_LINKS
                );
            case 's3':
                return new S3FilesystemAdapter(
                    new S3Client($config['credentials']),
                    $config['bucket'],
                    $config['prefix'] ?? '',
                    $config['options'] ?? []
                );
            case 'ftp':
                return new FtpFilesystemAdapter(
                    $config['host'],
                    $config['username'],
                    $config['password'],
                    $config['port'] ?? 21,
                    $config['root'] ?? '',
                    $config['ssl'] ?? false
                );
        }

        throw new InvalidArgumentException("Unsupported filesystem driver [{$config['driver']}]");
    }

    public function boot() {
        $this->ensureStorageDirectoryExists();
    }

    protected function ensureStorageDirectoryExists() {
        $paths = [
            APP_PATH . '/storage',
            APP_PATH . '/storage/app',
            APP_PATH . '/storage/framework',
            APP_PATH . '/storage/logs',
        ];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    public function provides() {
        return ['files', 'filesystem'];
    }}