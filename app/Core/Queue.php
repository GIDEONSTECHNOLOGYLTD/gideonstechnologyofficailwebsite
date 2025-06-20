class Queue {
    private $redis;
    private $logger;
    private const DEFAULT_QUEUE = 'default';
    private const MAX_ATTEMPTS = 3;

    public function __construct() {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->logger = Logger::getInstance();
    }

    public function push($job, $data = [], $queue = self::DEFAULT_QUEUE) {
        $payload = json_encode([
            'job' => $job,
            'data' => $data,
            'attempts' => 0,
            'created_at' => time()
        ]);

        return $this->redis->lPush("queue:$queue", $payload);
    }

    public function process($queue = self::DEFAULT_QUEUE) {
        while (true) {
            $payload = $this->redis->rPop("queue:$queue");
            
            if (!$payload) {
                sleep(1);
                continue;
            }

            try {
                $job = json_decode($payload, true);
                $job['attempts']++;

                if ($job['attempts'] > self::MAX_ATTEMPTS) {
                    $this->failed($job);
                    continue;
                }

                $this->runJob($job);
                
            } catch (\Exception $e) {
                $this->handleFailedJob($job, $e);
            }
        }
    }

    private function runJob($job) {
        try {
            $className = $job['job'];
            $instance = new $className();
            
            if (!method_exists($instance, 'handle')) {
                throw new \Exception("Job class must have handle method");
            }

            $instance->handle($job['data']);
            
            $this->logger->info("Job processed successfully", [
                'job' => $job['job'],
                'attempts' => $job['attempts']
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function handleFailedJob($job, \Exception $e) {
        if ($job['attempts'] < self::MAX_ATTEMPTS) {
            $this->retry($job);
        } else {
            $this->failed($job);
        }

        $this->logger->error("Job failed", [
            'job' => $job['job'],
            'attempts' => $job['attempts'],
            'error' => $e->getMessage()
        ]);
    }

    private function retry($job) {
        $payload = json_encode($job);
        $this->redis->lPush(
            "queue:" . self::DEFAULT_QUEUE,
            $payload
        );
    }

    private function failed($job) {
        $payload = json_encode([
            'job' => $job['job'],
            'data' => $job['data'],
            'attempts' => $job['attempts'],
            'failed_at' => time()
        ]);
        
        $this->redis->lPush('failed_jobs', $payload);
    }

    public function getQueueSize($queue = self::DEFAULT_QUEUE) {
        return $this->redis->lLen("queue:$queue");
    }

    public function clear($queue = self::DEFAULT_QUEUE) {
        return $this->redis->del("queue:$queue");
    }