<?php
namespace Module\Content\Action;
use Application\Access\Registered;
use Core\Event;

class Entry extends Event\Action
{
    /**
     * Grant access to this action only for registered users
     */
    use Registered;

	public function __invoke(int $contentID = 0)
	{
	    \getApplication()->setStylesheet('content.xsl');
	    $this->getOwner()->addNode($this->getOwner()->getEntryByID($contentID));
	}
}