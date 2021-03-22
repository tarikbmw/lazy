<?php
namespace Module\Authentication\Action;
use Application\Response\Accepted;
use Core\Event;

/**
 * Class Logout
 * @package Module\Authentication\Action
 */
class Logout extends Event\Action
{
    /**
     * @throws \Core\Exception\Error
     */
	public function __invoke()
	{
	    $this->getOwner()->setUser(NULL);
	    new Accepted($this, 'Bye!', '/');
	}
}