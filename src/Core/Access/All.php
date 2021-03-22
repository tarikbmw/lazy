<?php
namespace Core\Access;
use Core\Exception\Error;

/**
 * For all users
 * @author tarik
 *
 */
trait All
{
	function accessible():bool
	{
		return true;
	}
	
	/**
	 * Process acception of rule
	 */
	function acception():void
	{
	}

    /**
     * Rule rejection
     * In this case, this wouldn't be called
     * @throws Error
     */
	function rejection():void
	{
		throw new Error("Avaliable to all");
	}
}
