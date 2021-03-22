<?php
namespace Core;
/**
 * Class Queue
 * @package Core
 */
class Queue implements \Iterator
{
    private array   $array;
    private int     $position;

    /**
     * Queue constructor.
     * @param array|null $arrayObject
     */
    public function __construct(?array $arrayObject = null)
    {
        $this->position = 0;
        $this->array = (!empty($arrayObject) && count($arrayObject)) ? $arrayObject : [];
        
    }

    /**
     * Iterator current value
     * @return mixed
     */
    public function current():mixed
    {
        return $this->array[$this->position];
    }

    /**
     * Get key
     * @return int
     */
    public function  key():int
    {
        return $this->position;
    }

    /**
     * Move to next position
     */
    public function  next():void
    {
        ++$this->position;
    }

    /**
     * Revind position to start
     */
    public function rewind():void
    {
        $this->position = 0;
    }

    /**
     * Current position validation
     * @return bool
     */
    public  function valid():bool
    {
        return isset($this->array[$this->position]);
    }

    /**
     * Set new key with value
     * @param $key
     * @param $value
     */
    public function __set($key, $value):void
    {
        $this->array[] = $value;
    }

    /**
     * Get value by key
     * @param $key
     * @return mixed
     */
    public function __get($key):mixed
    {
        return $this->array[$key] ?? NULL;
    }
}