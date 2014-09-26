<?php namespace Clowdy\Cache;

use Illuminate\Cache\CacheManager as IlluminateCacheManager;
use Clowdy\Cache\MemcachedManager;
use Clowdy\Cache\MemcachedStore;

class CacheManager extends IlluminateCacheManager
{
    /**
     * Create an instance of the Memcached cache driver.
     *
     * @return \Illuminate\Cache\MemcachedStore
     */
    protected function createMemcachedDriver()
    {
        $memcached = new MemcachedManager($this->app, $this->app['memcached.connector']);

        return $this->repository(new MemcachedStore($memcached, $this->getPrefix()));
    }
}