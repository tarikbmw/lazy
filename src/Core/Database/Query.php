<?php
namespace Core\Database;
use Core\Cache;
use Core\Database\Exception as Error;

/**
 * Database query
 * @author tarik
 */
abstract class Query implements \Iterator
{
    use Result;

	/**
	 * Prepared statement instance
	 * @var mixed
	 */
	protected mixed $statement;
	
	/**
	 * Query result
	 * @var mixed
	 */
	protected mixed $result = [];

    /**
     * Caching instance
     * @var Cache|null
     */
	protected ?Cache $cache = NULL;

    /**
     * Cache expiration time in sec
     * @var int
     */
	protected int $cacheExpired = 0;

    /**
     * Flush cache and execute query, the new resultset will be stored in cache after query execution
     * @var bool
     */
	protected bool $cacheFlush = false;

    /**
     * Query constructor.
     * @param string            $query
     * @param Bindings|null     $bindings
     * @param Connector|null    $db
     * @throws Error\Query
     * @throws \Core\Exception\Error
     */
	public function __construct(protected string        $query = '',
                                protected ?Bindings     $bindings = NULL,
                                protected ?Connector    $db = NULL)
	{
		$this->bindings	    ??= new Bindings();
		$this->db           ??= \getApplication()->getDatabase();

		$this->statement = $this->db->getConnection()->prepare($this);
		if (!$this->statement)
			throw new Error\Query("Could not prepare statement `$this->query` {$this->db->getConnection()->error}.");
	}

	/**
	 * Executes query
	 * @return array|null 
	 */
	abstract public function execute():?array;
	
	/**
	 * Invokes the query
	 * @return array|null
	 */
	public function __invoke():?array
	{
		return $this->execute();
	}	
	
	/**
	 * Get query string
	 * @return string
	 */
	public function getQuery():string
	{
		return $this->query;
	}
		
	/**
	 * Get query bindings
	 * @return Bindings
	 */
	public function &getBindings():Bindings
	{
		return $this->bindings;
	}

	/**
	 * Get query execute result
	 * @return mixed
	 */
	public function getResult():mixed
	{
		return $this->result;
	}
	
	/**
	 * Bind parameter
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set(string $name, mixed $value):void
	{
		$this->bindings->$name = $value;
	}

    /**
     * Start to use cache for query result
     * @param int|null $expired  expiration time in sec
     */
	public function useCache(?int $expired = 0)
    {
        if ($this->cacheExpired && ($this->cacheExpired != $expired))
            $this->cacheFlush = true;

        $this->cacheExpired     = $expired;
        $this->cache            = \getApplication()->getCache();
    }

    /**
     * Flush cache before query execute
     */
    public function flushCache()
    {
        $this->cacheFlush = true;
    }

    /**
     * Get current database connector
     * @return \Connector|null
     */
    public function getConnector():?\Connector
    {
        return $this->db;
    }

    public function __toString():string
    {
	    return $this->query;
    }
}