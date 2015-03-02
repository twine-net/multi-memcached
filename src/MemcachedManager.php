<?php namespace Clowdy\Cache;

use Illuminate\Cache\MemcachedConnector as IlluminateMemcachedConnector;

class MemcachedManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The connector instance.
     *
     * @var \Illuminate\Cache\MemcachedConnector
     */
    protected $connector;

    /**
     * The active connection instances.
     *
     * @var array
     */
    protected $connections = array();

    /**
     * Create a new memcached manager instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($app, IlluminateMemcachedConnector $connector)
    {
        $this->app = $app;
        $this->connector = $connector;
    }

    /**
     * Get a memcached instance based on connection.
     *
     * @param  string     $name
     * @return \Memcached
     */
    public function connection($name = null)
    {
        $name = $this->parseConnectionName($name);

        // If we haven't created this connection, we'll create it based on the config
        // provided in the application.
        if (! isset($this->connections[$name])) {
            $connection = $this->makeConnection($name);

            $this->connections[$name] = $connection;
        }

        return $this->connections[$name];
    }

    /**
     * Parse the connection.
     *
     * @param  string $name
     * @return array
     */
    protected function parseConnectionName($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        return $name;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection()
    {
        return $this->app['config']['cache.memcached.default'];
    }

    /**
     * Set the default connection name.
     *
     * @param  string $name
     * @return void
     */
    public function setDefaultConnection($name)
    {
        $this->app['config']['cache.memcached.default'] = $name;
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Make the memcached instance based on connection.
     *
     * @param  string     $name
     * @return \Memcached
     */
    protected function makeConnection($name)
    {
        $config = $this->getConfig($name);

        return $this->connector->connect($config);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param  string $name
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getConfig($name)
    {
        $name = $name ?: $this->getDefaultConnection();

        // To get the memcached connection configuration, we will just pull each of the
        // connection configurations and get the configurations for the given name.
        // If the configuration doesn't exist, we'll throw an exception and bail.
        $connections = $this->app['config']['cache.memcached.connections'];

        if (is_null($config = array_get($connections, $name))) {
            throw new \InvalidArgumentException("Memcached [$name] not configured.");
        }

        return $config;
    }
}
