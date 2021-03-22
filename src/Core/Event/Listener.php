<?php
namespace Core\Event;
use Core\Feature\Access;
use Core\Exception\Error;
use Core\Module;
use Core\Pattern\Traits\Condition;

/**
 * Listen for events and process them
 * @author tarik
 */
class Listener
{
    use Condition;

    /**
     * Events to listen
     * @var array
     */
	protected array     $events = [];

    /**
     * Owner's instance
     * @var Module
     */
	protected Module    $owner;

	/**
	 * @param Module $owner - Listener owner
	 */
	function __construct(Module $owner)
	{
		$this->owner = $owner;
	}
	
	/**
	 * Bind action to event
	 * 
	 * @param string 			$eventName	- event name
	 * @param string 			$method		- event request method
	 * @param string 	        $className		- action to bind, string actions will be created with owner context before process it
	 */
	public function bind(string $eventName, string $method, string $className):void
	{
        $this->events[$eventName][$method][] = $className;
	}

    /**
     * Import events to listener
     * @param array $events already binded events
     */
	public function import(array $events):void
    {
        $this->events = $events;
    }

    /**
     * Process event
     * @param \Core\Event $event
     * @throws Error
     * @throws \ReflectionException
     */
	public function process(\Core\Event $event):void
	{
	    if (!isset($this->events[$event->getName()][$event->getMethod()]))
	        throw new Error("Action for event `{$event->getName()}` doesn't bind.");
	    
		$actions = $this->events[$event->getName()][$event->getMethod()] ?? NULL;
		if (empty($actions))
		    return;
		
		// Get variables
		$variables = $event->getMethod() == 'GET' ?
			$_GET : ($event->getMethod() == 'POST' ?
			$_POST : NULL);
					
		$actionClass = [];
		$invoke = [];

		foreach ($actions as $action)
		{
		    if (!$action instanceof \stdClass)
		        throw new Error('Mismatched action type for '.$event->getName());
		        
			$hAction = new $action->class($this->owner);

            // Check processing conditions
		    foreach ($this->getConditions() as $condition)
                if ($condition['condition']($hAction))
                    $condition['handler']($hAction);

			// Get invoke method by reflection
			if (!isset($actionClass[$action->class]))
			     $actionClass[$action->class] 	= new \ReflectionClass( $action->class );

			if (!isset($invoke[$action->class]))
			     $invoke[$action->class] 		= $actionClass[$action->class]->getMethod('__invoke');
		
			// Sort invoke parameters
			$parametersOut = array();
			foreach ($invoke[$action->class]->getParameters() as $var)
				$parametersOut[] =  $variables[$var->getName()] ?? $var->getDefaultValue();
						
			// Invoke
			$invoke[$action->class]->invokeArgs($hAction, $parametersOut);
		}
	}
}