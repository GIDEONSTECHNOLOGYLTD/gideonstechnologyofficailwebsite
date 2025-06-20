class ViewServiceProvider extends ServiceProvider {
    public function register() {
        $this->registerViewFinder();
        $this->registerViewEngine();
        $this->registerBladeCompiler();
        $this->registerViewFactory();
    }

    protected function registerViewFinder() {
        $this->singleton('view.finder', function() {
            return new ViewFinder(APP_PATH . '/views');
        });
    }

    protected function registerViewEngine() {
        $this->singleton('view.engine', function() {
            return new ViewEngine();
        });
    }

    protected function registerBladeCompiler() {
        $this->singleton('blade.compiler', function() {
            return new BladeCompiler(
                APP_PATH . '/cache/views'
            );
        });
    }

    protected function registerViewFactory() {
        $environment = config('app.environment');

        $this->singleton('view', function($app) use ($environment) {
            return $environment === 'production'
                ? new ProductionViewFactory($app)
                : new DevelopmentViewFactory($app);
        });
    }

    public function boot() {
        $this->registerDirectives();
        $this->registerComponents();
        $this->registerIncludes();
    }

    protected function registerDirectives() {
        $blade = $this->container->make('blade.compiler');

        $blade->directive('datetime', function($expression) {
            return "<?php echo date('Y-m-d H:i:s', strtotime($expression)); ?>";
        });

        $blade->directive('money', function($expression) {
            return "<?php echo number_format($expression, 2); ?>";
        });

        $blade->directive('routeIs', function($expression) {
            return "<?php if (request()->routeIs($expression)): ?>";
        });

        $blade->directive('endrouteIs', function() {
            return "<?php endif; ?>";
        });
    }

    protected function registerComponents() {
        $view = $this->container->make('view');

        $view->component('button', \App\View\Components\Button::class);
        $view->component('card', \App\View\Components\Card::class);
        $view->component('alert', \App\View\Components\Alert::class);
        $view->component('modal', \App\View\Components\Modal::class);
    }

    protected function registerIncludes() {
        $viewFinder = $this->container->make('view.finder');
        
        $viewFinder->addNamespace('layouts', APP_PATH . '/views/layouts');
        $viewFinder->addNamespace('components', APP_PATH . '/views/components');
        $viewFinder->addNamespace('partials', APP_PATH . '/views/partials');
    }
}