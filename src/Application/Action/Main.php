<?php
namespace Application\Action;
use Core\Event;
use Core\Render\Stylesheet\XSL;
use Core\Database\MySql\Query;
use Application\Entity;
use Module\Authentication;
use Module\Content;

/**
 * 
 * Default applicatoin event
 * @author tarik
 *
 */
class Main extends Event\Action
{
	public function __invoke()
	{
	    \getApplication()->setStylesheet('main.xsl');

	    $content = Content::getInstance()->getEntries();
        $this->getOwner()->addNode(new Entity('content', NULL, $content));
	}
}