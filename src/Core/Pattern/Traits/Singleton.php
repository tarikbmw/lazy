<?php
namespace Core\Pattern\Traits;

/**
 * Trait for Singleton pattern
 * @author tarik
 */
trait Singleton
{
    /**
     * Current instance
     * @var self
     */
    private static $Instance;

    /**
     * Get current instance or create new one
     * @return static
     */
	static function getInstance():self
	{
        return static::$Instance ?? (static::$Instance = new static());
	}
}