<?php
namespace Core\Pattern;

/**
 * Interface Singleton
 * @package Core\Pattern
 */
interface Singleton
{
    /**
     * Get current instance or create new one
     * @return self
     */
	public static function getInstance():self;
}
