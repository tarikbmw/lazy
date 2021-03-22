<?php
namespace Core;
use Core\Event;
use Core\Pattern\Singleton;
use Core\Feature\{Node,Exportable};

/**
 * Abstract class describes module
 * @author tarik
 */
abstract class Module implements Exportable, Node, Singleton
{
    use \Core\Pattern\Traits\Node;

    /**
     * Module export name, you need to define it in derived class
     */
    private const MODULE_NAME = 'module';

	/**
	 * Event listener
	 * @var Event\Listener
	 */
	private	?Event\Listener	$eventListener = NULL;

    /**
     * Module constructor.
     * Setup module name
     */
	protected function __construct()
    {
        $this->setKey(static::MODULE_NAME);
    }

	/**
	 * Get current event listener
	 * @return \Core\Event\Listener
	 */
	public function getListener():Event\Listener
	{
		return  $this->eventListener instanceof Event\Listener ? 
		              $this->eventListener : 
		              $this->eventListener = new Event\Listener($this); 
	}
	
	/**
	 * Process render
	 */
	protected function process():void
	{
	}
}
