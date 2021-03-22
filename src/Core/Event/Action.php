<?php
namespace Core\Event;
use Core\Feature\Access;
use Core\Module;

/**
 * Action abstract class
 * @author tarik
 */
abstract class Action implements Access
{
	/**
	 * Using access layer for all
	 */
    use \Core\Access\All;

	/**
	 * Construct
	 * @param Module $owner action owner
	 */
	public function __construct(private Module $owner)
	{
	}

	/**
	 * Get module, action's owner
	 * @return Module
	 */
	public function getOwner():Module
	{
	    return $this->owner;
	}
		
	/**
	 * Process event
	 */
	abstract public function __invoke();
}
