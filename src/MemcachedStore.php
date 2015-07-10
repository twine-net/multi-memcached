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
}
