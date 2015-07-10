<?php

namespace Clowdy\Cache;

use Illuminate\Cache\CacheServiceProvider as IlluminateCacheServiceProvider;

class CacheServiceProvider extends IlluminateCacheServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bindShared('cache', function ($app) {
            return new CacheManager($app);
        });

        $this->app->bindShared('cache.store', function ($app) {
            return $app['cache']->driver();
        });

        $this->app->bindShared('memcached.connector', function () {
            return new MemcachedConnector();
        });

        $this->registerCommands();
    }
}
