abstract class FormRequest {
    protected $data;
    protected $errors = [];
    protected $validator;

    public function __construct(array $data = []) {
        $this->data = $data ?: $_POST;
        $this->validator = new Validator();
    }

    abstract public function rules(): array;
    
    abstract public function authorize(): bool;

    public function validate() {
        if (!$this->authorize()) {
            throw new \Exception('Unauthorized access');
        }

        $this->beforeValidation();
        
        if (!$this->validator->make($this->data, $this->rules())) {
            $this->errors = $this->validator->getErrors();
            return false;
        }

        $this->afterValidation();
        return true;
    }

    protected function beforeValidation() {}
    
    protected function afterValidation() {}

    public function getErrors() {
        return $this->errors;
    }

    public function validated() {
        if (!empty($this->errors)) {
            throw new \Exception('Form not validated');
        }

        $validated = [];
        foreach ($this->rules() as $field => $rule) {
            if (isset($this->data[$field])) {
                $validated[$field] = $this->data[$field];
            }
        }

        return $validated;
    }

    public function only(array $fields) {
        return array_intersect_key($this->validated(), array_flip($fields));
    }

    public function except(array $fields) {
        return array_diff_key($this->validated(), array_flip($fields));
    }

    public function has($field) {
        return isset($this->data[$field]);
    }

    public function filled($field) {
        return $this->has($field) && !empty($this->data[$field]);
    }

    public function missing($field) {
        return !$this->has($field);
    }

    public function get($field, $default = null) {
        return $this->data[$field] ?? $default;
    }

    public function input($key = null, $default = null) {
        if ($key === null) {
            return $this->data;
        }
        return $this->get($key, $default);
    }

    public function boolean($field) {
        return filter_var($this->get($field), FILTER_VALIDATE_BOOLEAN);
    }

    public function integer($field) {
        return filter_var($this->get($field), FILTER_VALIDATE_INT);
    }

    public function float($field) {
        return filter_var($this->get($field), FILTER_VALIDATE_FLOAT);
    }

    public function email($field) {
        return filter_var($this->get($field), FILTER_VALIDATE_EMAIL);
    }

    public function url($field) {
        return filter_var($this->get($field), FILTER_VALIDATE_URL);
    }

    public function file($field) {
        return $_FILES[$field] ?? null;
    }

    public function hasFile($field) {
        return isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function merge(array $data) {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function replace(array $data) {
        $this->data = $data;
        return $this;
    }

    public function flash() {
        $_SESSION['_old_input'] = $this->data;
        $_SESSION['_errors'] = $this->errors;
    }

    public static function old($key = null, $default = null) {
        if ($key === null) {
            return $_SESSION['_old_input'] ?? [];
        }
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}