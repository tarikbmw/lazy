<?php
namespace Application\Access;
use Core\Exception\Error;
use Module\Authentication\Entity\Account;

/**
 * For guests
 * @author tarik
 *
 */
trait Guest
{
    /**
     * Accessible not for logged in users
     * @return bool
     */
	function accessible():bool
	{
		return !\Module\Authentication::getInstance()->isLoggedIn();
	}
	
	/**
	 * Process acception of rule
	 */
	function acception():void
	{
	    return;
	}
	
	function rejection():void
	{
		throw new Error("Данная страница доступна только незарегистрированным пользователям!");
	}
}