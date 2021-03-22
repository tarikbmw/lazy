<?php
namespace Core;
use Core\Module\Descriptor;
use Core\Render;
use Core\Log;
use Core\Exception\Error;
use Core\Database\Connector;
use Core\Pattern\Traits\Condition;

/**
 * Framework main class
 * Your custom application class must be derived from this.
 * @author tarik
 */
abstract class Framework extends Module implements \Stringable
{	
    use Condition;
    
    /**
     * Application configuration instance
     * @var Setup
     */
    private Setup $config;
	
    /**
     * Logging instance
     * @var Log
     */
    private Log $log;

    /**
     * Database connector
     * @var array
     */
    private array $database = [];

    /**
     * Array of modules
     * @var array
     */
    private array $modules = [];

    /**
     * Array of current events
     * @var array
     */
    private	array $events = [];

    /**
     * Current render
     * @var Render
     */
    private	?Render	$render = NULL;

    /**
     * Output mode
     * @var string
     */
    private string $output;

    /**
     * Modules was processed list
     * @var array
     */
    private array $processedModules = [];
    
    /**
     * Session instance
     * @var Session
     */
    private ?Session $session = NULL;

    /**
     * @var Cache|null
     */
    private ?Cache      $cache = NULL;

    /**
     * Framework constructor
     * @param string $configuration - path to an application settings XML file
     * @throws Error
     */
    protected function __construct(string $configuration = Setup::SETTINGS)
    {
        $this->config = Setup::getInstance($configuration);

        // Adding modules from configuration
        foreach($this->config->getModules() as $key=>$item)
            $this->addModule($item->name, $item->class, $item->actions);

        // Set base framework options
        $action = $_GET['action'] ?? NULL;
        $module = $_GET['module'] ?? NULL;
        $this->output = $_GET['output'] ?? $this->getConfig()->getApplication()->output;

        // Binding default event
        $default = $this->getConfig()->getApplication()->action;
        $this->getListener()->bind($default->name, 'GET', $default->class);

        // Add event by action
        if ($action)
            $this->addEvent($module, $action, $this->getMethod());
        else
            $this->addEvent($default->module, $default->name); 		// Add default event
    }

    /**
     * Get server request method
     * @return string
     */
	public function getMethod():string
    {
        return  $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

	/**
	 * (non-PHPdoc)
	 * @see \Core\Module::__invoke()
	 */
	public function __invoke():void
	{
        while(count($this->events))
        {
            $module = array_key_first($this->events);
            $queue = array_shift($this->events);

            foreach($queue as $key => $event)
               $this->processModule($module, $event);
        }

		$this->process();
	}

	/**
	 * Rendering to output
	 */
	protected function process():void
	{
	    foreach ($this->processedModules as $module)
            $this->getRender()->addNode($module);

	    $this->getRender()->process();
	}
		
	/**
	 * Get current database instance or creates new one
	 * @var string $connectorID use different ID for each database connection
	 * @return Connector
	 */
	public function getDatabase(?string $connectorID = NULL):\Core\Database\Connector
	{
	    $connectorID ??= $this->getConfig()->getApplication()->database;

	    if (isset($this->database[$connectorID]) && $this->database[$connectorID] instanceof \Core\Database\Connector)
	        return $this->database[$connectorID];
				
        $className = $this->config->getDatabase($connectorID)->class;
        if (!class_exists($className))
            throw new Error("Database connector class `$className` is not found.");
        
    	return $this->database[$connectorID] 	= new $className($connectorID);
	}
		
	/**
	 * Get current render or creates new one
	 * @return Render
     * @throws Error
	 */
	public function getRender():Render
	{
		if ($this->render instanceof Render)
			return $this->render;

		$output = $this->getConfig()->getOutput($this->getOutputType()) or
		    throw new Error("Render for `{$this->getOutputType()}` doesn't not set.");

		if (!class_exists($output->class))
            throw new Error("Couldn't found render class `$output->class` for `{$this->getOutputType()}`.");

		return $this->render = new $output->class();
	}
		
	/**
	 * Get current log instance or creates new one
	 * @return \Core\Log
	 */
	public function getLog():Log
	{
	    return ($this->log instanceof Log) ? $this->log : ($this->log =  new Log());
	}
	
	/**
	 * Get current configuration instance 
	 * @return mixed
	 */
	public function getConfig():Setup
	{
        return $this->config;
	}

	/**
	 * Adding new module descriptor to application
	 * @param string $moduleName 	- module name
	 * @param string $className 	- module class name
     * @param array  $router        - module events router
	 */
	protected function addModule(string $moduleName, string $className, ?array $router = []):void
	{
		$this->modules[$moduleName] = new Descriptor($className, $router);
	}
		
	/**
	 * Get modules, or get module class descriptor
	 * @param string $moduleName - module name
	 * @return Descriptor
	 */
	public function getModule(string $moduleName):?Descriptor
	{
		return $this->modules[$moduleName] ?? NULL;
	}
		
	/**
	 * Adding new event to application's queue
	 * @param string 	$module	- target module name
	 * @param string 	$name	- event's name
	 * @param string 	$method	- event type (post, get, @see \Core\Event)
	 * @throws Error
	 */
	public function addEvent(string $module, string $name, string $method = 'GET'):void
	{
		if ( !$this->getModule($module) )
			throw new Error("Module `{$module}` doesn't registered.");
		
		if (!isset($this->events[$module]))
			$this->events[$module] = new Queue();
		
		$this->events[$module]->add = new Event($name, $method);
	}
		
    /**
     * Setup inbound variables for all events by method
     * @param array $variables
     * @param string $method
     */
	public function setEventVariables(array $variables, string $method = 'GET'):void
	{
	   switch ($method)
	   {
	       case 'GET':
	       default:
	           $_GET = array_merge($_GET, $variables);
	       break;
	       
	       case 'POST':
	           $_POST = array_merge($_POST, $variables);
	       break;
	   }    
	}
	
	/**
	 * Processing module
	 * @param string $module	- module name
	 * @param Event $event		- current event to process
     * @throws Error
	 */
	protected function processModule(string $module, Event $event):void
    {
	    $moduleDescriptor = $this->getModule($module);
	    if (!class_exists($moduleDescriptor->getClassName()))
	        throw new Error("Could not find class `{$moduleDescriptor->getClassName()}` for module `$module`.");

	    $moduleInstance = ($moduleDescriptor->getClassName())::getInstance();
        $moduleInstance->getListener()->import($this->getConfig()->getModule($module)->actions);

	    foreach ($this->conditions as $condition)
	        $moduleInstance->getListener()->registerCondition($condition['condition'], $condition['handler']);

        $moduleInstance->getListener()->process($event);

        $this->processedModules[$module] = $moduleInstance;
	}

	/**
	 * Get render output type
	 * @return string
	 */
	public function getOutputType():string
	{
	    return $this->output;
	}
	
	/**
	 * Get session object
	 * @param string $sessionID - external session id
	 * @throws Error
	 * @return Session
	 */
	public function getSession(?string $sessionID = NULL):Session
	{
	    if ($this->session)
	        return $this->session;
	    
	    $session = $this->getConfig()->getSession();
	    if (!$session || !$session->class)
	        throw new Error('Missed configuration parameter session. Session class is not defined.');

	    return $this->session = $session->class::getInstance($sessionID);
	}

    /**
     * Get cache instance
     * @return Cache
     */
	public function getCache():\Core\Cache
    {
        if ($this->cache)
            return $this->cache;

        $cache = $this->getConfig()->getCache();
        $className = $cache->class;
        return $this->cache = $className::getInstance();
    }
}