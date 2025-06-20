class ValidationServiceProvider extends ServiceProvider {
    public function register() {
        $this->singleton('validator', function() {
            return new ValidatorManager($this->container);
        });

        $this->singleton('validator.factory', function() {
            return new ValidatorFactory(
                $this->container->make('translator')
            );
        });
    }

    public function boot() {
        $this->registerRules();
        $this->registerImplicitRules();
        $this->registerDependentRules();
    }

    protected function registerRules() {
        $validator = $this->container->make('validator');

        $validator->extend('unique', function($attribute, $value, $parameters) {
            return $this->container->make('db')
                ->table($parameters[0])
                ->where($parameters[1] ?? $attribute, $value)
                ->count() === 0;
        });

        $validator->extend('exists', function($attribute, $value, $parameters) {
            return $this->container->make('db')
                ->table($parameters[0])
                ->where($parameters[1] ?? $attribute, $value)
                ->exists();
        });

        $validator->extend('strong_password', function($attribute, $value) {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $value);
        });
    }

    protected function registerImplicitRules() {
        $validator = $this->container->make('validator');

        $validator->extendImplicit('required_if', function($attribute, $value, $parameters) {
            $other = $this->getValue($parameters[0]);
            $values = array_slice($parameters, 1);

            if (in_array($other, $values)) {
                return $value !== null && $value !== '';
            }

            return true;
        });

        $validator->extendImplicit('required_unless', function($attribute, $value, $parameters) {
            $other = $this->getValue($parameters[0]);
            $values = array_slice($parameters, 1);

            if (!in_array($other, $values)) {
                return $value !== null && $value !== '';
            }

            return true;
        });
    }

    protected function registerDependentRules() {
        $validator = $this->container->make('validator');

        $validator->extendDependent('confirmed', function($attribute, $value) {
            return $value === $this->getValue($attribute . '_confirmation');
        });

        $validator->extendDependent('different', function($attribute, $value, $parameters) {
            return $value !== $this->getValue($parameters[0]);
        });

        $validator->extendDependent('same', function($attribute, $value, $parameters) {
            return $value === $this->getValue($parameters[0]);
        });
    }

    protected function getValue($field) {
        return $this->container->make('request')->input($field);
    }

    public function provides() {
        return ['validator', 'validator.factory'];
    }}