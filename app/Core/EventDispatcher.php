class EventDispatcher {
    private static $instance = null;
    private $listeners = [];
    private $logger;

    private function __construct() {
        $this->logger = Logger::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addListener($event, callable $listener, $priority = 0) {
        $this->listeners[$event][$priority][] = $listener;
        ksort($this->listeners[$event]);
    }

    public function dispatch($event, array $data = []) {
        if (!isset($this->listeners[$event])) {
            return;
        }

        try {
            foreach ($this->listeners[$event] as $priority => $listeners) {
                foreach ($listeners as $listener) {
                    $listener($data);
                }
            }
            
            $this->logger->debug("Event '$event' dispatched", [
                'data' => $data,
                'listeners_count' => count($this->listeners[$event])
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Error dispatching event '$event'", [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function removeListener($event, callable $listener) {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $priority => $listeners) {
            $key = array_search($listener, $listeners, true);
            if ($key !== false) {
                unset($this->listeners[$event][$priority][$key]);
            }
        }
    }

    public function hasListeners($event) {
        return isset($this->listeners[$event]) && !empty($this->listeners[$event]);
    }

    public function clearListeners($event = null) {
        if ($event === null) {
            $this->listeners = [];
        } else {
            unset($this->listeners[$event]);
        }
    }

    private function __clone() {}
    private function __wakeup() {}