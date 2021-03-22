<?php
namespace Core\Pattern\Iterable;

/**
 * Interface Factory
 * @package Core\Pattern
 */
interface Factory
{
    /**
     * Produces objects iterator
     * @return \Iterator
     */
	public function spawn():\Traversable;
}