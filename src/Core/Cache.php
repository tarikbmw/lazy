<?php
namespace Core;
use Core\Pattern\Singleton;

/**
 * Cache support
 * @package Core
 */
abstract class Cache implements Singleton
{
    use Pattern\Traits\Singleton;

    abstract protected function __construct();

    /**
     * Easy access to cache storage
     * @param string $name key stored to cache
     * @return mixed
     */
    public function __get(string $name):mixed
    {
        return $this->get($name) ?? NULL;
    }

    /**
     * Flushing cache
     * @return mixed
     */
    public function flush()
    {
        return $this->getHandler()->flush();
    }

    /**
     * Get variable by key
     * @param string $key
     * @return mixed
     */
    public function get(string $key):mixed
    {
        return $this->getHandler()->get($key);
    }

    /**
     * Set variable
     * @param string $key
     * @param $value
     * @param int|null $expired
     * @return mixed
     */
    abstract public function set(string $key, $value, ?int $expired = 0):bool;

    /**
     * Replacing variable
     * @param string $key
     * @param $value
     * @param int|null $expired
     * @return mixed
     */
    abstract public function replace(string $key, $value, ?int $expired = 0):bool;
}