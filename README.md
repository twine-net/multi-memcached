Multiple Memcached Connections
===============

Multiple memcached connection handler for Laravel cache and elasticache support. There is also support for `getMulti`, `putMulti`, `foreverMulti` and `forgetMulti` specifically for memcached only.

__The cache driver can not be swapped if you make use of the additional `getMulti`, `putMulti`, `foreverMulti` or `forgetMulti` functions or the `get`, `put`, `forever`, `forget` with arrays. They are specific for memcached only!__

## Installation

You can install the package using the [Composer](https://getcomposer.org/) package manager:

```json
{
    "require": {
        "clowdy/multi-memcached": "0.2.*"
    }
}
```

Update `app/config/app.php` with the new service provider.

```php
'providers' => array(
    ...
    //'Illuminate\Cache\CacheServiceProvider',
    'Clowdy\Cache\CacheServiceProvider',
    ...
)
```

## Configuration

The package makes use the the existing memcached configs in `app/config/cache.php`, with a slightly modified structure.

Example:
```php
...
'memcached' => array(
    'default' => 'data1',

    'connections' => array(

        // cluster
        'data1' => array(
            array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
            array('host' => '127.0.0.1', 'port' => 11212, 'weight' => 100)
        ),

        // single node
        'data2' => array(
            array('host' => '127.0.0.1', 'port' => 11213, 'weight' => 100),
        ),

        // elasticache cluster
        'data3' => array(
            'elasticache' => true
            'servers' => array(
                array('host' => 'memcached.cache.amazonaws.com', 'port' => 11211, 'weight' => 100),
            ),
        )
    )
),
```

Any other providers in Laravel that make use of memcached will use the default connection. For example the `session` provider using the `memcached` driver will use the default connection.

## Example Usage

```php
Cache::connection('data1')->get('somekey');

// or you can omit the connection method to use the default connection.

Cache::get('somekey');

// also perform a multi get using an array

Cache::get(['key1', 'key2']);
```

or

```php
<?php namespace Some\App;

use Illuminate\Cache\Repository as CacheRepository;

class SomeClass
{
    protected $cache;

    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    public function update($data)
    {
        $this->cache->put('key', $data, 60);
        $this->update2($data);
    }

    public function update2($data)
    {
        $this->cache->connection('data2')->put('key', $data, 60);
        $this->update3(['key' => $data]);
    }

    public function update3(array $data)
    {
        $this->cache->connection('data3')->put(array_keys($data), array_values($data), 60);

        // or
        
        $this->cache->connection('data3')->putMulti($data, 60);
    }
}
```
