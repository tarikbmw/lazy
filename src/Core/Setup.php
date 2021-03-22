<?php 
namespace Core;
use Core\Pattern\Singleton;

/**
 * Class Setup
 * Setup application using a settings file
 * @package Core
 */
class Setup implements Singleton
{
    use \Core\Pattern\Traits\Singleton;

    /**
     * Default framework settings
     */
    const SETTINGS = "../cfg/Application.xml";    

    /**
     * Default module's action namespace
     */
    const ACTION_NAMESPACE = '\Action\\';

    /**
     * Application settings descriptor
     * @var \stdClass
     */
	private \stdClass   $application;

    /**
     * Session settings descriptor
     * @var \stdClass
     */
	private \stdClass   $session;

    /**
     * Output type settings descriptors
     * @var array
     */
	private array       $output = [];

    /**
     * Database connector settings descriptors
     * @var array
     */
	private array       $database = [];

    /**
     * Stylesheet settings descriptor
     * @var \stdClass
     */
	private \stdClass   $stylesheet;

    /**
     * Log settings descriptor
     * @var \stdClass
     */
	private \stdClass   $log;

    /**
     * Module settings descriptors
     * @var array
     */
	private array       $module = [];

    /**
     * Cache settings descriptor
     * @var \stdClass
     */
	private \stdClass   $cache;

    /**
     * Get current instance or create new one
     * @param string $config path to an application settings XML file
     * @return static
     * @throws \Exception
     */
    static function getInstance(string $config = self::SETTINGS):self
    {
        return static::$Instance ?? (static::$Instance = new static($config));
    }

    /**
     * Setup constructor
     * This will setup the application configuration by settings file
     * @param string $config path to an application settings XML file
     * @throws \Exception
     */
	protected function __construct(string $config = self::SETTINGS)
	{
	    $xml = new \XMLReader();
	    if (!$xml->open($config, 'utf-8'))
	        throw new \Exception("Couldn't read configuration file.");

        $this->application  = new \stdClass();
        $this->session      = new \stdClass();
        $this->stylesheet   = new \stdClass();
        $this->log          = new \stdClass();
        $this->cache        = new \stdClass();

        $inModule = NULL;

        while ($xml->read())
        {
            if ($xml->nodeType == \XMLReader::END_ELEMENT)
            {
                if ($xml->name == 'Module')
                    $inModule = NULL;

                continue;
            }

            switch ($xml->name)
            {
                case 'Application':
                    $this->application->name     = $xml->getAttribute('name');
                    $this->application->class    = $xml->getAttribute('class');
                    $this->application->output   = $xml->getAttribute('output');
                    $this->application->charset  = $xml->getAttribute('charset');
                    $this->application->database = $xml->getAttribute('database');
                    break;

                case 'Session':
                    $this->session->class   = $xml->getAttribute('class');
                    $this->session->name   = $xml->getAttribute('name');
                    $xml->read();
                    $this->session->key     = $xml->value;
                    break;

                case 'WorkingDirectory':
                    $dir            = new \stdClass();
                    $dir->path      = $xml->getAttribute('path');
                    $dir->uri       = $xml->getAttribute('uri');

                    $this->application->directory = $dir;
                    break;

                case 'Output':
                    $name = $xml->getAttribute('name');
                    $this->output[$name] = new \stdClass();
                    $this->output[$name]->name = $xml->getAttribute('name');
                    $this->output[$name]->class = $xml->getAttribute('class');
                    $this->output[$name]->mime = $xml->getAttribute('mime');
                    break;

                case 'Database':
                    $db = new \stdClass();
                    $db->class      = $xml->getAttribute('class');
                    $db->name       = $xml->getAttribute('name');
                    $db->hostname   = $xml->getAttribute('hostname');
                    $db->login      = $xml->getAttribute('login');
                    $db->password   = $xml->getAttribute('password');
                    $db->schema     = $xml->getAttribute('schema');
                    $db->charset    = $xml->getAttribute('charset');
                    $this->database[$db->name] = $db;
                    break;

                case 'Stylesheet':
                    $this->stylesheet->path         = $xml->getAttribute('path');
                    $this->stylesheet->default      = $xml->getAttribute('default');
                    $this->stylesheet->uri          = $xml->getAttribute('uri');
                    $this->stylesheet->version      = $xml->getAttribute('version');
                    break;

                case 'Log':
                    $this->log->enabled             = $xml->getAttribute('enabled') == 'true';
                    $this->log->path                = $xml->getAttribute('path');
                    $this->log->name                = $xml->getAttribute('name');
                    $this->log->showDate            = $xml->getAttribute('showDate') == 'true';
                    $this->log->filenameDateFormat  = $xml->getAttribute('filenameDateFormat');
                    $this->log->dateFormat          = $xml->getAttribute('dateFormat');
                    break;

                case 'DefaultAction':
                    $act = new \stdClass();
                    $act->name  = $xml->getAttribute('name');
                    $act->module  = $xml->getAttribute('module');
                    $act->class = $xml->getAttribute('class');

                    $this->application->action = $act;
                    break;

                case 'Module':
                    $inModule = $xml->getAttribute('name');
                    $this->module[$inModule] = new \stdClass();
                    while($xml->moveToNextAttribute())
                        $this->module[$inModule]->{$xml->name} = $xml->value;
                    break;

                case 'Action':
                    if (!$inModule)
                        throw new \Exception('Could not parse Action node without Module parent.');

                    $act = new \stdClass();
                    $act->name      = $xml->getAttribute('name');
                    $class          = $xml->getAttribute('class');
                    $act->class     = $this->module[$inModule]->class.self::ACTION_NAMESPACE.$class;
                    if ($class[0] == '\\')
                        $act->class = $class;
                        
                    $act->method    = $xml->getAttribute('method');

                    $this->module[$inModule]->actions[$act->name][$act->method][] = $act;
                    break;

                case 'Cache':
                    $this->cache->name       = $xml->getAttribute('name');
                    $this->cache->hostname   = $xml->getAttribute('hostname');
                    $this->cache->port       = $xml->getAttribute('port');
                    $this->cache->class      = $xml->getAttribute('class');
                    break;
                default:
            }
        }

        $xml->close();
	}

    /**
     * Get all modules descriptors
     * @return array
     */
	public function getModules():array
    {
        return $this->module;
    }

    /**
     * Get module descriptor by name
     * @param string $moduleName - module name
     * @return \stdClass|null
     */
    public function getModule(string $moduleName):?\stdClass
    {
        return $this->module[$moduleName] ?? NULL;
    }

    /**
     * Get session descriptor
     * @return \stdClass
     */
    public function getSession():\stdClass
    {
        return $this->session;
    }

    /**
     * Get application descriptor
     * @return \stdClass
     */
	public function getApplication():\stdClass
    {
        return $this->application;
    }

    /**
     * Get database connector descriptor by name
     * @param string $name - connector name
     * @return \stdClass|null
     */
    public function getDatabase(string $name):?\stdClass
    {
        return $this->database[$name] ?? NULL;
    }

    /**
     * Get output descriptor by type
     * @param string $type - output type (xml,html, etc.)
     * @return \stdClass|null
     */
    public function getOutput(string $type):?\stdClass
    {
        return $this->output[$type] ?? NULL;
    }

    /**
     * Get stylesheet descriptor
     * @return \stdClass
     */
    public function getStylesheet():\stdClass
    {
        return $this->stylesheet;
    }

    /**
     * Get log descriptor
     * @return \stdClass
     */
    public function getLog():\stdClass
    {
        return $this->log;
    }

    /**
     * Get cache descriptor
     * @return \stdClass|null
     */
    public function getCache():?\stdClass
    {
        return $this->cache ?? NULL;
    }
}
