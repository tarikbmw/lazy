<?php
namespace Module\Authentication\Action;
use Core\Event;

/**
 * Class Account
 * Get user account if logged in
 * @package Module\Authentication\Action
 */
class Account extends Event\Action
{
	public function __invoke()
	{
        $this->getOwner()->addNode($this->getOwner()->getUser());
    }
}