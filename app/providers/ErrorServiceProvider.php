class ErrorServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('error.handler', function() {
            return new ErrorHandler(
                $this->container->make('logger'),
                $this->container->make('view')
            );
        });

        $this->registerExceptionHandlers();
    }

    protected function registerExceptionHandlers() {
        $handler = $this->container->make('error.handler');

        $handler->register(ValidationException::class, function($e) {
            return ApiResponse::validation($e->errors());
        });

        $handler->register(AuthenticationException::class, function($e) {
            return ApiResponse::unauthorized($e->getMessage());
        });

        $handler->register(AuthorizationException::class, function($e) {
            return ApiResponse::forbidden($e->getMessage());
        });

        $handler->register(NotFoundException::class, function($e) {
            return ApiResponse::notFound($e->getMessage());
        });

        $handler->register(TokenMismatchException::class, function($e) {
            return ApiResponse::error('CSRF token mismatch', 419);
        });

        $handler->register(RateLimitExceededException::class, function($e) {
            return ApiResponse::error('Too Many Requests', 429);
        });

        $handler->register(QueryException::class, function($e) {
            $this->container->make('logger')->error('Database error', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return ApiResponse::error('Database error occurred', 500);
        });
    }

    public function boot() {
        $this->registerErrorHandlers();
        $this->registerShutdownHandler();
    }

    protected function registerErrorHandlers() {
        set_error_handler(function($level, $message, $file = '', $line = 0) {
            if (error_reporting() & $level) {
                throw new ErrorException($message, 0, $level, $file, $line);
            }
        });

        set_exception_handler(function($e) {
            $this->container->make('error.handler')->handle($e);
        });
    }

    protected function registerShutdownHandler() {
        register_shutdown_function(function() {
            $error = error_get_last();
            
            if ($error !== null && $error['type'] === E_ERROR) {
                $this->container->make('error.handler')
                    ->handleFatal($error);
            }
        });
    }

    protected function shouldReportException($e) {
        return !$this->isExceptionIgnored($e);
    }

    protected function isExceptionIgnored($e) {
        $ignored = [
            AuthenticationException::class,
            ValidationException::class,
            NotFoundException::class
        ];

        foreach ($ignored as $ignoredClass) {
            if ($e instanceof $ignoredClass) {
                return true;
            }
        }

        return false;
    }}