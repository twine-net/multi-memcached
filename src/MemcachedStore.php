<?php

namespace Clowdy\Cache;

use Illuminate\Cache\MemcachedStore as IlluminateMemcachedStore;

class MemcachedStore extends IlluminateMemcachedStore
{
    /**
     * The Memcached manager instance.
     *
     * @var Clowdy\Cache\MemcachedManager
     */
    protected $memcachedManager;

    /**
     * Create a new Memcached store.
     *
     * @param Clowdy\Cache\MemcachedManager $memcachedManager
     * @param string                        $prefix
     */
    public function __construct(MemcachedManager $memcachedManager, $prefix = '')
    {
        $this->memcachedManager = $memcachedManager;

        parent::__construct($this->connection()->memcached, $prefix);
    }

    /**
     * Sets the memcached instance to one from the connection.
     *
     * @return \Illuminate\Cache\MemcachedStore
     */
    public function connection($name = null)
    {
        $this->memcached = $this->memcachedManager->connection($name);

        return $this;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (is_array($key)) {
            return $this->getMulti($key);
        }

        return parent::get($key);
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param mixed $key
     * @param mixed $value
     * @param int   $minutes
     */
    public function put($key, $value, $minutes)
    {
        if (is_array($key)) {
            $items = array_combine($key, $value);

            return $this->putMulti($items, $minutes);
        }

        return parent::put($key, $value, $minutes);
    }

    /**
     * Remove an item from the cache.
     *
     * @param mixed $key
     */
    public function forget($key)
    {
        if (is_array($key)) {
            return $this->forgetMulti($key);
        }

        return parent::forget($key);
    }

    /**
     * Retrieve an array of items from the cache by key.
     *
     * @param array $key
     * @param array $tokens
     *
     * @return array
     */
    public function getMulti(array $keys, $tokens = null)
    {
        $values = $this->memcached->getMulti($this->prefixKeys($keys), $tokens, \Memcached::GET_PRESERVE_ORDER);

        if ($this->memcached->getResultCode() == 0) {
            return $values;
        }
    }

    /**
     * Store an array of items in the cache for a given number of minutes.
     *
     * @param array $items
     * @param int   $minutes
     */
    public function putMulti(array $items, $minutes)
    {
        $this->memcached->setMulti($this->prefixKeys($items), $minutes * 60);
    }

    /**
     * Store an array of items in the cache indefinitely.
     *
     * @param  array  $items
     * @return void
     */
    public function foreverMulti(array $items)
    {
        return $this->putMulti($items, 0);
    }

    /**
     * Remove an array of items from the cache.
     *
     * @param array $key
     */
    public function forgetMulti(array $keys)
    {
        $this->memcached->deleteMulti($this->prefixKeys($keys));
    }

    /**
     * Prefix and array of keys with the cache prefix.
     *
     * @param array $key
     *
     * @return array
     */
    protected function prefixKeys(array $keys)
    {
        $result = [];
        array_walk($keys, function ($value, $key) use (&$result) {
            $result[$this->prefix.$key] = $value;
        });

        return $result;
    }
}
