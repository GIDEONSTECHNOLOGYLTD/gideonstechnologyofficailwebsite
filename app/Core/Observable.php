trait Observable {
    protected static $observers = [];
    protected static $events = [
        'creating', 'created',
        'updating', 'updated',
        'deleting', 'deleted',
        'saving', 'saved'
    ];

    public static function observe($class) {
        static::$observers[] = new $class;
    }

    protected function fireEvent($event, $params = []) {
        foreach (static::$observers as $observer) {
            if (method_exists($observer, $event)) {
                $observer->$event($this, $params);
            }
        }
    }

    protected function fireSaving() {
        $this->fireEvent('saving');
    }

    protected function fireSaved() {
        $this->fireEvent('saved');
    }

    protected function fireCreating() {
        $this->fireEvent('creating');
    }

    protected function fireCreated() {
        $this->fireEvent('created');
    }

    protected function fireUpdating() {
        $this->fireEvent('updating');
    }

    protected function fireUpdated() {
        $this->fireEvent('updated');
    }

    protected function fireDeleting() {
        $this->fireEvent('deleting');
    }

    protected function fireDeleted() {
        $this->fireEvent('deleted');
    }

    public function save() {
        $this->fireSaving();
        
        if (isset($this->attributes[$this->primaryKey])) {
            $this->fireUpdating();
            $result = $this->update();
            $this->fireUpdated();
        } else {
            $this->fireCreating();
            $result = $this->insert();
            $this->fireCreated();
        }
        
        $this->fireSaved();
        return $result;
    }

    public function delete() {
        $this->fireDeleting();
        $result = parent::delete();
        $this->fireDeleted();
        return $result;
    }
}