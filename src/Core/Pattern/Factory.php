<?php
namespace Core\Pattern;

/**
 * Interface Factory
 * @package Core\Pattern
 */
interface Factory
{
    /**
     * Creates array of objects
     * @return array|null
     */
	public function spawn();
}