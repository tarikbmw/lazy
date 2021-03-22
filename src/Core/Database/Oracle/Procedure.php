<?php
namespace Core\Database\Oracle;
use Core\Database\Bindings;
use Core\Database\Connector;

/**
 * Class Procedure
 * @package Core\Database\Oracle
 */
class Procedure extends \Core\Database\Oracle\Query
{
    /**
     * Template with return parameter binding
     */
	const CALL_TEMPLATE 			= 'begin %s := %s(%s); end;';

    /**
     * Simple call template
     */
	const CALL_TEMPLATE_NO_RETURN 	= 'begin %s(%s); end;';

    /**
     * Return parameter bind name
     */
    const RETURN_BIND_NAME          = ':returnCode';

    /**
     * Return parameter key, for searching it in binding collection
     */
	const RETURN_NAME 		        = 'procedureReturnValue';

    /**
     * Init query with empty parameter
     * @var string
     */
	protected string $query = '';

    /**
     * Procedure constructor.
     * @param string            $name
     * @param Bindings|null     $bindings
     * @param Connector|null    $db
     * @throws \Core\Database\Exception\Query
     * @throws \Core\Exception\Error
     */
    public function __construct(
        private string          $name,
        protected ?Bindings     $bindings = NULL,
        protected ?Connector    $db = NULL)
    {
        $this->bindings ??= new Bindings();
        $this->db ??= \getApplication()->getDatabase();
    }

    /**
     * Get generated query request or creates new one with bindings
     * @return string
     */
	public function getQuery():string
	{
		if ($this->query)
			return $this->query;

		$parametersIn = [];
		foreach ($this->getBindings() as $key=>$parameter)
			if ($key != self::RETURN_NAME)
				$parametersIn[$key] = $parameter->getName();
			
		if (isset($this->bindings[self::RETURN_NAME]))
			return $this->query	= sprintf(self::CALL_TEMPLATE, self::RETURN_BIND_NAME, $this->name, implode(',',$parametersIn));

		return $this->query	= sprintf(self::CALL_TEMPLATE_NO_RETURN, $this->name, implode(',',$parametersIn));
	}

    /**
     * Create procedure's return parameter
     * @param mixed|null $value
     * @param int $type
     * @param int $length
     */
	public function createReturnParameter(mixed $value = NULL, $type = \SQLT_CHR, $length = 32):void
	{
	    if ($value instanceof Blob)
        {
            $this->bindings[self::RETURN_NAME] = $value;
            return;
        }

		$this->bindings[self::RETURN_NAME] = new Parameter(self::RETURN_BIND_NAME, $value, $type, $length, 'return');
	}

    /**
     * Get return parameter
     * @return Parameter
     */
    public function getReturn():Parameter
    {
        return $this->bindings[self::RETURN_NAME];
    }

    /**
     * Export key
     * @return string
     */
	public function getKey():string
	{
		return 'procedure';
	}

    /**
     * Export attributes
     * @return array|null
     */
    public function getAttributes():?array
    {
        return ['name' => str_replace(['"', "'", '`'], '', $this->name)];
    }

    /**
     * Export nodes
     * @return array|null
     */
    public function getNodes():?array
    {
        return $this->getBindings();
    }
}