<?php
namespace Core\Database\Oracle;
use Core\Database\Request;
use Core\Database\Exception as Error;

/**
 * Oracle interface
 * @author 	tarik
 */
class Connector extends \Core\Database\Connector
{
    /**
     * Connector constructor.
     * @param string|null $connectorID
     * @throws \Exception
     */
    public function __construct(?string $connectorID = NULL)
    {
        $connectorID ??= \getApplication()->getConfig()->getApplication()->database;
        $db = \getApplication()->getConfig()->getDatabase($connectorID);
        if (!$db)
            throw new Error("Database connector `$connectorID` was missed in configuration.");

        $this->charset = $db->charset ?? 'UTF-8';

        parent::__construct(    $db->hostname,
                                $db->schema,
                                $db->login,
                                $db->password);

        $this->connect();
    }

	function __destruct()
	{
		if (!is_resource($this->db))
		    return;

        oci_close($this->db);
        $this->db = NULL;
	}

	/**
	 * Connect to database
	 * @throws Error
	 */
	function connect():?object
	{
		if ($this->db)
			return null;

		$this->db = oci_pconnect($this->login, $this->password, $this->host, $this->charset);
        $error = oci_error();
		if (!$this->db || $error)
		{
            if (!$error)
                $error = ['message' => 'Unknown error while connecting to database', 'code' => 0];

            throw new Error\Connect($error['message'], $error['code'] );
		}

        $query = oci_parse($this->db, "alter session set  NLS_NUMERIC_CHARACTERS = '.  '");
        oci_execute($query);

		return null;
	}

    /**
     * Bind parameter to prepared statement
     * @param \Core\Database\Query $request
     * @param \Core\Database\Parameter $parameter
     * @return bool
     */
	public function bind(\Core\Database\Query $request, \Core\Database\Parameter $parameter):bool
	{
        return oci_bind_by_name(
            $request->getStatement(),
            $parameter->getName(),
            $parameter->getValue(),
            $parameter->getLength(),
            $parameter->getType());
	}

    /**
     * Prepare statement
     * @param \Core\Database\Query $request
     * @throws Error\Query
     */
	public function prepare(\Core\Database\Query $request):void
	{
	    $statement = oci_parse($this->db, $request->getQuery());
	    $request->setStatement($statement);
		foreach($request->getBindings() as $parameter)
    		if (!$this->bind($request, $parameter))
            {
                $error = oci_error($request->getStatement()) or
                    throw new Error\Query('Unknown error while binding parameters');
                throw new Error\Query("Error binding parameter {$parameter->getName()}: {$error['message']}, {$error['sqltext']}");
            }
	}

    /**
     * Execute query with prepared statement
     * @param \Core\Database\Query $request
     * @return bool
     * @throws Error\Query
     */
	public function execute(\Core\Database\Query $request)
	{
        $statement = $request->getStatement();
        if (!is_resource($statement))
			throw new Error\Query("Could not execute null statement.");

		if (!oci_execute($statement, OCI_DEFAULT))
		{
			$error = oci_error();
            if (!$error)
                $error = ['message' => 'Unknown error while executing query', 'code' => 0];

			throw new Error\Query($error['message'], $error['code']);
		}
	}

    /**
     * Fetch query result to array
     * @param \Core\Database\Query $request
     * @return array|null
     * @throws Error\Invalid
     * @throws Error\Query
     */
	public function fetch(\Core\Database\Query $request):?array
    {
        $statement = $request->getStatement();
        if (!is_resource($statement))
            throw new Error\Query("Could not fetch null statement.");

        $result = [];
        oci_fetch_all($statement,$result, 0, -1, \OCI_FETCHSTATEMENT_BY_ROW + \OCI_ASSOC);
        if (!$result)
        {
            $error = oci_error($statement);
            if (!$error)
                return $result;

            throw new Error\Invalid($error['message'], $error['code']);
        }

        oci_free_statement($statement);
        return $result;
    }

    /**
     * Adding this layer as OOP API for oracle
     * @return self
     */
    public function getConnection():self
    {
        return $this;
    }

    /**
     * Return OCI descriptor
     * @return mixed
     */
    public function getDescriptor():mixed
    {
        return $this->db;
    }
}