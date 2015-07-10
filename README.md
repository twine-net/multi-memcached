Multiple Memcached Connections
===============

Multiple memcached connection handler for Laravel cache and elasticache support.

## Installation

You can install the package using the [Composer](https://getcomposer.org/) package manager:

```json
{
    "require": {
        "clowdy/multi-memcached": "dev-master"
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
        'data1' => array(
            // cluster
            array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
            array('host' => '127.0.0.1', 'port' => 11212, 'weight' => 100)
        ),

        'data2' => array(
            // single node
            array('host' => '127.0.0.1', 'port' => 11213, 'weight' => 100),
        ),

        'data3' => array(
            // single node
            array('host' => '127.0.0.1', 'port' => 11214, 'weight' => 100),
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
    }
}
```
