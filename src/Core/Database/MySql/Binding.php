<?php
namespace Core\Database\MySql;

/**
 * Trait Binding
 * @package Core\Database\MySql
 */
trait Binding
{
    /**
     * Get binding type
     * @param mixed $binding
     * @return string
     */
    public function getType(mixed $binding):string
    {
        return match(gettype($binding))
        {
            default             => 's',
            'integer'           => 'i',
            'float', 'double'   => 'd'
        };
    }
}