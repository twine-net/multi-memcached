Multiple Memcached Connections
===============

Multiple memcached connection handler for Laravel cache.

First you need to replace `Illuminate\Cache\CacheServiceProvider` with `Clowdy\Cache\CacheServiceProvider`. The `Cache` facade works as normal.

Next update the `app/config/cache.php` `memcached`. Example:

```php
'memcached' => array(
    'default' => 'session',

    'session' => array(
        array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
        array('host' => '127.0.0.1', 'port' => 11212, 'weight' => 100)
    ),

    'data' => array(
        array('host' => '127.0.0.1', 'port' => 11213, 'weight' => 100),
    ),

    'other' => array(
        array('host' => '127.0.0.1', 'port' => 11214, 'weight' => 100),
    ),
),
```