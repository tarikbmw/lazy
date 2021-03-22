<?php
namespace Core\Database\MySql;
use Core\Exception\Error;
use Core\Setup;
use Core\Database\Exception;

/**
 * MySql connector
 * @author TARiK
 */
class Connector extends \Core\Database\Connector
{
    /**
     * Database default charset
     * @var string
     */
    private string $charset;
    
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
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Database\Connector::connect()
     */
    function connect():?object
    {
        if ($this->db instanceof \mysqli)
            return $this->db;
        
        $this->db = new \mysqli($this->host, $this->login, $this->password);
        if (!$this->db || $this->db->connect_error)
            throw new Exception\Connect(mysqli_connect_error());

        unset($this->host);
        unset($this->login);
        unset($this->password);

        $this->query("set names '{$this->charset}'");
        if ($this->schema && !$this->setSchema($this->schema))
            throw new \Core\Exception\Error("Unable to set schema `{$this->schema}`.");

        return $this->db;
    }
    
    /**
     * Select default schema
     * @param string $schema - schema name
     * @return bool
     */
    function setSchema(?string $schema = NULL):bool
    {
        return $this->db->select_db($schema ?? $this->schema);
    }
    
    /**
     * Executes the query
     * @param string $query
     * @throws Exception\Query
     * @return bool
     */
    function query(string $query):bool
    {
        $this->queries[] = $this->lastCall = $this->db->query($query);
        //$this->runTimes++;
        
        if (!$this->lastCall)
            throw new Exception\Query($this->db->error." for query :".$query);
            
        return true;
    }
    

    /**
     * Fetch the result
     * @param mixed $result
     * @return array
     */
    function fetch($result=NULL):?array
    {
        $result ??= $this->lastCall;
            
        if (!is_object($result))
            return NULL;

        return $result->fetch_array();
    }
    
    
    /**
     * Get the fetched row count
     * @param mixed $result - current result
     * @return int
     */
    function rows($result = NULL):int
    {
        return ($result ?? $this->last_call)->num_rows;
    }
    
    
    /**
     * Get the last query inserted Id
     * @return int
     * @throws Exception\Query
     */
    function getInsertedId():int
    {
        $this->query("select last_insert_id()");
        return ($this->fetch())[0];
    }
}
