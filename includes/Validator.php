<?php
class Validator {
    private $errors = [];
    private $data = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty($this->data[$field])) {
            $this->addError($field, $message ?? "$field is required");
        }
        return $this;
    }

    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?? "$field must be a valid email");
        }
        return $this;
    }

    public function minLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->addError($field, $message ?? "$field must be at least $length characters");
        }
        return $this;
    }

    public function maxLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->addError($field, $message ?? "$field must not exceed $length characters");
        }
        return $this;
    }

    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->addError($field, $message ?? "$field must be numeric");
        }
        return $this;
    }

    public function matches($field, $matchField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$matchField]) && 
            $this->data[$field] !== $this->data[$matchField]) {
            $this->addError($field, $message ?? "$field must match $matchField");
        }
        return $this;
    }

    public function pattern($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->addError($field, $message ?? "$field has an invalid format");
        }
        return $this;
    }

    public function custom($field, callable $callback, $message = null) {
        if (isset($this->data[$field]) && !$callback($this->data[$field])) {
            $this->addError($field, $message ?? "$field is invalid");
        }
        return $this;
    }

    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function passes() {
        return empty($this->errors);
    }

    public function fails() {
        return !$this->passes();
    }

    public function getValue($field, $default = null) {
        return $this->data[$field] ?? $default;
    }
}