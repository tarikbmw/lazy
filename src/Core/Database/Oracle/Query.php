<?php
namespace Core\Database\Oracle;
use Core\Database\Bindings;
use Core\Database\Connector;
use Core\Database\Exception as Error;
use Core\Database\Result;
use Core\Feature\Exportable;
use Core\Feature\Export;

class Query extends \Core\Database\Query implements Exportable, Export\Nodes, Export\Attributes
{
    use Result;

    /**
     * Execution flag
     * @var bool
     */
    private bool $bExecuted = false;

    /**
     * Return variable bind name
     */
	const RETURN_BIND_NAME = ':return';

    /**
     * Return variable key in binding list
     */
	const RETURN_NAME = 'returnValue';

    /**
     * For query result row
     * @var int
     */
	private int $position = 0;

    /**
     * Query constructor.
     * @param string $query
     * @param Bindings|null $bindings
     * @param Connector|null $db
     * @throws Error\Query
     * @throws \Core\Exception\Error
     */
	public function __construct(
	    protected string        $query,
        protected ?Bindings     $bindings = NULL,
        protected ?Connector    $db = NULL)
	{
	    parent::__construct($this->query, $this->bindings, $this->db);
	}
	
	public function __get($name):mixed
	{
		return $this->bindings[$name] ?? NULL;
	}
	
	public function __set($name, $value):void
	{
		$sz = count($this->bindings);

		if (($value instanceof Parameter) || ($value instanceof Cursor) || ($value instanceof Blob))
            $this->bindings[$name] = $value;
		elseif ($name == self::RETURN_NAME)
            $this->bindings[$name] = new Parameter(self::RETURN_BIND_NAME, $value, $this->getType($value), 32, $name);
		else
		    $this->bindings[$name] = new Parameter(':param'.$sz, $value, $this->getType($value), -1, $name);
	}

	public function &getBindings():Bindings
	{		
		return $this->bindings;
	}

	public function getStatement():mixed
	{
		return $this->statement;
	}
	
	public function setStatement(mixed $statement)
	{
		if (!is_resource($statement))
			throw new Error\Query("Invalid statement set.");

		$this->statement = $statement;
	}
	
    /**
     * Get sql type of binding
     * @param mixed $binding
     * @return int
     * @throws Error\Query
     */
	protected function getType(mixed $binding):int
	{
	    return match(gettype($binding))
        {
            default => \SQLT_CHR,
            'integer'           => \SQLT_INT,
            'float', 'double'   => \SQLT_LNG,
            'object'            =>
                ($binding instanceof Cursor || $binding instanceof Blob) ?
                    $binding->getType() :
                    throw new Error\Query('Invalid binding data type.')
        };
	}

    /**
     * @return array|null
     */
	public function execute():?array
	{
        if ($this->bExecuted)
            return $this->result;

        $db = $this->db->getConnection();
        $db->prepare($this);
        $db->execute($this);

        $this->bExecuted = true;

        return [];
    }

    /**
     * Fetch result
     * @return null
     */
    public function fetch():mixed
    {
        if (!$this->bExecuted)
            return NULL;

        return $this->result = $this->db->getConnection()->fetch($this);
    }

    /**
     * @return array|null
     */
	public function __invoke():?array
	{
        return $this->execute();
	}

    /**
     * @return string
     */
	public function getKey():string
	{
		return 'query';
	}

    /**
     * @return array|null
     */
	public function getAttributes():?array
	{
		return ['content' => str_replace(array('"', "'", '`'), '', $this->query)];
	}

    /**
     * @return array|null
     */
	public function getNodes():?array
	{
		return $this->parameters;
	}

    /**
     * Display query sql code with parameters
     * @return string
     */
    public function dump():string
    {
        $output = $this->getQuery()."\r\n";
        foreach ( $this->getBindings() as $parameter)
            $output .= "\t".$parameter->getName() ." = ". $parameter->getValue()."\r\n";

        return $output;
    }

    /**
     * Iterator current
     * @return mixed
     */
    public function current():mixed
    {
        return $this->result;
    }

    /**
     *  Iterator next
     */
    public function next():void
    {
        $this->position++;
    }

    /**
     * Iterator key
     * @return int
     */
    public function key():int
    {
        return $this->position;
    }

    /**
     * Iterator valid
     * @return bool
     */
    public function valid():bool
    {
        $this->fetch();
        return isset($this->result);
    }

    /**
     * Iterator rewind
     */
    public function rewind():void
    {
        $this->position = 0;
    }
}
