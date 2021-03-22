<?php 
namespace Core\Database\Oracle;
use Core\Database;
use Core\Exception\Error;
use Core\Database\Exception\Invalid;

/**
 * Oracle cursor support
 * @author tarik
 */
class Cursor implements Database\Parameter, \Iterator
{
    use Database\Result;

	/**
	 * Binding name template
	 * @var string
	 */
	const BIND_TEMPLATE = ":cursor%s";
	
	/**
	 * Total cursors count to complete its naming
	 * @var integer
	 */
	private static int $totalCursors = 0;
	
	/**
	 * Current cursor bind name
	 * @var string
	 */
	private string $bindName;
	
	/**
	 * Database instance
	 * @var \Core\Database\Oracle\Oracle
	 */
	private Connector $db;
	
	/**
	 * OCI cursor
	 * @var unknown
	 */
	private $cursor;
	
	/**
	 * Iterator current row
	 * @var mixed
	 */
	private $row;
	
	/**
	 * Iterator current position
	 * @var integer
	 */
	private int $position = 0;
	
	/**
	 * Cursor execution flag
	 * @var boolean
	 */
	private bool $isExecuted = false;

    /**
     * Current request
     * @var Query|null
     */
	private ?Query $request;

    /**
     * Cursor constructor.
     * @param Query|null $request  use this for cache
     * @throws Error
     * @throws Invalid
     */
	public function __construct(?Query $request=NULL)
	{
		$this->db       = $request ? $request->getConnector() : \getApplication()->getDatabase();
		$this->request  = $request;
		$this->cursor = oci_new_cursor($this->db->getConnection()->getDescriptor());
		if (!$this->cursor)
			throw new Invalid("Failed to create new cursor.");

        $this->bindName = sprintf(self::BIND_TEMPLATE, self::$totalCursors++);
	}

	/**
	 *  Free cursor statement
	 */
	public function __destruct()
	{
		if (is_resource($this->cursor))
			oci_free_statement($this->cursor);
	}
	
	/**
	 * Execute cursor
	 */
	public function __invoke()
	{
		$this->execute();
	}

    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind():void
    {
        $this->position = 0;
        $this->isExecuted = false;

        $this->execute();
    }

    /**
     * (non-PHPdoc)
     * @see \Core\Database\Parameter::getValue()
     */
    public function &getValue():mixed
    {
        return $this->cursor;
    }

	/**
	 * (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid():bool
	{
		$this->row = oci_fetch_array($this->cursor, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
		$err = oci_error($this->cursor);
		if ($err)
			throw new Error($err['message']);
		
		return (bool)$this->row;
	} 
	
	/**
	 * (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next():void
	{
		$this->position++;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current():mixed
	{
		return $this->row;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key():int
	{
		return $this->position;
	}

	/**
	 * Execute cursor
	 * @throws Error
	 */
	public function execute():void
	{
		if ($this->isExecuted)
			return;

		$return = oci_execute($this->cursor, OCI_DEFAULT);		
		if (!$return || oci_error($this->cursor))
			throw new Error(oci_error($this->cursor));
		
		$this->isExecuted = true;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Core\Database\Parameter::getName()
	 */
	public function getName():string
	{
		return $this->bindName;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\Database\Parameter::getType()
	 */
	public function getType():int
	{
		return \OCI_B_CURSOR;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\Database\Parameter::getLength()
	 */
	public function getLength():int
	{
		return -1;
	}

    /**
     * Dump cursor data to string
     * @return string
     * @throws Error
     */
	public function dump():string
    {
        $result = '';
        $this->execute();

        foreach($this as $item)
            $result.=print_r($item, true);

        return $result;
    }
}
