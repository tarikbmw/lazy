<?php
namespace Application\Access;
use Core\Exception\Error;
use Module\Authentication\Entity\Account;

/**
 * For registered user only rule
 * @author tarik
 *
 */
trait Registered
{
    /**
     * Check the rule
     * Accessible for logged in users only
     * @return boolean
     */
	function accessible():bool
	{
		return \Module\Authentication::getInstance()->isLoggedIn();
	}
	
	/**
	 * Process the rule acception
	 * @return void
	 */
	function acception():void
	{
	   return;   
	}
	
	/**
	 * Process the rule rejection
	 * @throws Error
	 */
	function rejection():void
	{
		throw new Error("You need to login.");
	}
}