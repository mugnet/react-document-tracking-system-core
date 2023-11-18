<?php

namespace Libraries\Cache;

use Interfaces\Cacheable;
use Traits\Singleton;

/**
 * Reponsible for caching data into memory
 * @link https://en.wikipedia.org/wiki/Cache_(computing)
 */
class MemoryCache implements Cacheable
{
    use Singleton;

    /**
     * The cached data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Get a cached data
     *
     * @param string $key
     * @param mixed|null $default returned if the key does not exist
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            /**
             * @var Item
             */
            $item = unserialize($this->data[$this->resolve($key)]);

            if ($item->expired()) {
                $this->remove($key);

                return $default;
            }

            return $item->value;
        }
        return $default;
    }

    /**
     * Cache a data
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function set($key, $value)
    {
        $this->data[$this->resolve($key)] = serialize(new Item($key, $value));
        return $this;
    }

    /**
     * Checks if a key exists
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        if (in_array($this->resolve($key), array_keys($this->data))) {
            /**
             * @var Item
             */
            $item = unserialize($this->data[$this->resolve($key)]);

            if ($item->expired()) {
                $this->remove($key);

                return false;
            }

            return true;
        }

        return false;
    }

    public function remove($key)
    {
        unset($this->data[$this->resolve($key)]);

        return $this;
    }

    /**
     * Stores a callable and returns its value
     *
     * @param string $key
     * @param callable $callable
     * @return mixed
     */
    public function store($key, $callable)
    {
        if (!$this->has($key)) {
            $this->set($key, $callable());
        }
        return $this->get($key);
    }

    protected function resolve($key)
    {
        return sprintf('%s_%s', session()->id(), $key);
    }
}
