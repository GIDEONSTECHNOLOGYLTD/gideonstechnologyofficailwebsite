namespace App\Services;

use App\Core\Cache as CoreCache;

class Cache {
    private $redis;
    private const DEFAULT_TTL = 3600;

    public function __construct() {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function get($key) {
        try {
            $value = $this->redis->get($key);
            return $value ? json_decode($value, true) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function set($key, $value, $ttl = self::DEFAULT_TTL) {
        try {
            $value = json_encode($value);
            return $this->redis->setex($key, $ttl, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete($key) {
        try {
            return $this->redis->del($key);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function clear($pattern = '*') {
        try {
            $keys = $this->redis->keys($pattern);
            if (!empty($keys)) {
                return $this->redis->del($keys);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function remember($key, $ttl, callable $callback) {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    public function increment($key, $by = 1) {
        try {
            return $this->redis->incrBy($key, $by);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function decrement($key, $by = 1) {
        try {
            return $this->redis->decrBy($key, $by);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function tags(array $tags) {
        return new CacheTag($this, $tags);
    }

    public function flush() {
        try {
            return $this->redis->flushDB();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getMultiple(array $keys) {
        try {
            $values = $this->redis->mget($keys);
            return array_map(function ($value) {
                return $value ? json_decode($value, true) : null;
            }, array_combine($keys, $values));
        } catch (\Exception $e) {
            return array_fill_keys($keys, null);
        }
    }

    public function setMultiple(array $values, $ttl = self::DEFAULT_TTL) {
        try {
            $pipeline = $this->redis->multi();
            
            foreach ($values as $key => $value) {
                $pipeline->setex($key, $ttl, json_encode($value));
            }
            
            return $pipeline->exec();
        } catch (\Exception $e) {
            return false;
        }
    }