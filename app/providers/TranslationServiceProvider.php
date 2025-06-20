class TranslationServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('translator', function() {
            $config = Config::getInstance();
            $locale = $config->get('app.locale', 'en');
            $fallbackLocale = $config->get('app.fallback_locale', 'en');
            
            $translator = new Translator($locale);
            $translator->setFallback($fallbackLocale);
            
            $this->loadTranslations($translator);
            return $translator;
        });
    }

    protected function loadTranslations(Translator $translator) {
        $path = APP_PATH . '/resources/lang';
        if (!is_dir($path)) return;

        foreach (glob($path . '/*', GLOB_ONLYDIR) as $locale) {
            $locale = basename($locale);
            foreach (glob($path . "/{$locale}/*.php") as $file) {
                $group = basename($file, '.php');
                $translations = require $file;
                $translator->addResource('array', $translations, $locale, $group);
            }
        }
    }

    public function provides() {
        return ['translator'];
    }
}