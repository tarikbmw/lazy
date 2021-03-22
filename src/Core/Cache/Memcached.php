<?php
namespace Core\Cache;
use Core\Cache;

/**
 * Class Memcached
 * @package Core\Cache
 */
class Memcached extends Cache
{
    /**
     * Memcached instance handler
     * @var \Memcached
     */
    private \Memcached $handler;

    /**
     * Memcached constructor.
     * @throws \Exception
     */
    protected function __construct()
    {
        $cfg = \getApplication()->getConfig()->getCache();

        $this->handler = new \Memcached($cfg->name) or
            throw new \Exception('Memcached initialization failed.');

        $servers = $this->handler->getServerList();
        if (count($servers))
            foreach ($servers as $server)
                if ($server['host'] == $cfg->hostname && ($server['port'] == $cfg->port|| !$cfg->port))
                    return;

        $this->handler->addserver($cfg->hostname, $cfg->port);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $expired
     * @return bool
     */
    public function set(string $key, mixed $value, ?int $expired = 0):bool
    {
        return $this->handler->set($key, $value, $expired);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $expired
     * @return bool
     */
    public function replace(string $key, mixed $value, ?int $expired = 0):bool
    {
        return $this->handler->replace($key, $value, $expired);
    }

    /**
     * Accessing to Memcached instance handler
     * @return \Memcached
     */
    protected function getHandler():\Memcached
    {
        return $this->handler;
    }
}
