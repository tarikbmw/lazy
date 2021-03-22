<?php
namespace Core\Database;
use Core\Pattern\Singleton;

/**
 * Abstract database connector
 * @author tarik
 */
abstract class Connector
{    
	/**
	 * Database handler
	 * @var mixed
	 */
	protected mixed $db          = NULL;

    /**
     * Connector constructor.
     * @param string $host
     * @param string $schema
     * @param string $login
     * @param string $password
     * @param string $prefix Database table prefix
     * @param bool $bPersist Use persistent connection
     */
	public function __construct(
	    protected string $host,
        protected string $schema,
        protected string $login,
        protected string $password,
        protected string $prefix = '',
        protected bool $bPersist = true)
	{
	}
		
	/**
	 * Get the connection instance
	 * @return mixed
	 */
	public function getConnection():mixed
	{
		return $this->db ??= $this->connect();
	}

	/**
	 * Connect to database
	 * @return mixed
	 */
	abstract public function connect():?object;
}