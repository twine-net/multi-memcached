<?php namespace Clowdy\Cache;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\CacheServiceProvider as LaravelCacheServiceProvider;
use Clowdy\Cache\CacheManager;
use Illuminate\Cache\MemcachedConnector;

class CacheServiceProvider extends LaravelCacheServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('cache', function($app)
        {
            return new CacheManager($app);
        });

        $this->app->bindShared('cache.store', function($app)
        {
            return $app['cache']->driver();
        });

        $this->app->bindShared('memcached.connector', function()
        {
            return new MemcachedConnector;
        });

        $this->registerCommands();
    }
}