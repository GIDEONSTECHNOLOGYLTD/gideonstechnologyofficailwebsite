class Validator {
    private $errors = [];
    private $data = [];
    private $rules = [];

    public function make(array $data, array $rules) {
        $this->data = $data;
        $this->rules = $rules;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $this->validateField($field, $fieldRules);
        }

        return empty($this->errors);
    }

    private function validateField($field, $rules) {
        $rules = explode('|', $rules);
        $value = $this->data[$field] ?? null;

        foreach ($rules as $rule) {
            if (strpos($rule, ':') !== false) {
                [$ruleName, $parameter] = explode(':', $rule);
            } else {
                $ruleName = $rule;
                $parameter = null;
            }

            if (!$this->processRule($field, $value, $ruleName, $parameter)) {
                break;
            }
        }
    }

    private function processRule($field, $value, $rule, $parameter = null) {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'The :field field is required');
                    return false;
                }
                break;

            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'The :field must be a valid email');
                    return false;
                }
                break;

            case 'min':
                if (is_string($value) && strlen($value) < $parameter) {
                    $this->addError($field, "The :field must be at least $parameter characters");
                    return false;
                }
                break;

            case 'max':
                if (is_string($value) && strlen($value) > $parameter) {
                    $this->addError($field, "The :field may not be greater than $parameter characters");
                    return false;
                }
                break;

            case 'numeric':
                if ($value && !is_numeric($value)) {
                    $this->addError($field, 'The :field must be a number');
                    return false;
                }
                break;

            case 'alpha':
                if ($value && !ctype_alpha($value)) {
                    $this->addError($field, 'The :field must only contain letters');
                    return false;
                }
                break;

            case 'alphanumeric':
                if ($value && !ctype_alnum($value)) {
                    $this->addError($field, 'The :field must only contain letters and numbers');
                    return false;
                }
                break;

            case 'url':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, 'The :field must be a valid URL');
                    return false;
                }
                break;

            case 'date':
                if ($value && strtotime($value) === false) {
                    $this->addError($field, 'The :field must be a valid date');
                    return false;
                }
                break;

            case 'array':
                if (!is_array($value)) {
                    $this->addError($field, 'The :field must be an array');
                    return false;
                }
                break;
        }

        return true;
    }

    private function addError($field, $message) {
        $this->errors[$field][] = str_replace(':field', $field, $message);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getFirstError($field = null) {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }
        
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        
        return null;
    }