<?php
namespace Core\Cache;
use Core\Cache;

/**
 * Class Memcache
 * @package Core\Cache
 */
class Memcache extends Cache
{
    /**
     * Memcache instance handler
     * @var \Memcache
     */
    private \Memcache $handler;

    /**
     * Memcache constructor.
     * @throws \Exception
     */
    protected function __construct()
    {
        $cfg = \getApplication()->getConfig()->getCache();

        $this->handler = new \Memcache($cfg->name) or
            throw new \Exception('Memcache initialization failed.');

        $this->handler->addserver($cfg->hostname, $cfg->port);
    }

    public function __destruct()
    {
        if ($this->handler)
            $this->handler->close();
    }

    /**
     * Some magic with requests and its parameters
     * @param string $key
     * @param mixed $value
     * @param int|null $expired
     * @param string $type
     * @return bool
     */
    private function handleRequest(string $key, mixed $value, ?int $expired = 0, string $type = 'set'):bool
    {
        if (!$key)
            return false;

        $flag = is_bool($value) || is_int($value) || is_float($value) ? false : \MEMCACHE_COMPRESSED;
        return $this->handler->$type($key, $value, $flag, $expired);
    }

    /**
     * Cache set method
     * @param string $key
     * @param mixed $value
     * @param int|null $expired
     * @return bool
     */
    public function set(string $key, mixed $value, ?int $expired = 0):bool
    {
        return $this->handleRequest($key, $value, $expired);
    }

    /**
     * Cache replace method
     * @param string $key
     * @param mixed $value
     * @param int|null $expired
     * @return bool
     */
    public function replace(string $key, mixed $value, ?int $expired = 0):bool
    {
        return $this->handleRequest($key, $value, $expired, 'replace');
    }

    /**
     * Accessing to Memcache instance handler
     * @return \Memcache
     */
    protected function getHandler():\Memcache
    {
        return $this->handler;
    }
}