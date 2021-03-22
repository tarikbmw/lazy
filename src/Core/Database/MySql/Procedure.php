<?php
namespace Core\Database\MySql;
use Core\Database\Bindings;

/**
 * MySql stored procedure call
 * @author tarik
 */
class Procedure extends \Core\Database\MySql\Query
{
    /**
     * Routine call template
     * @var string
     */
    const CALL_TEMPLATE = "call %s;";
    
    /**
     * Constructor
     * @param string    $procedureName usage: routineName(?,?,?)
     * @param Bindings  $bindings
     * @param Connector $db
     */
    public function __construct(string $procedureName, ?Bindings $bindings = NULL, ?Connector $db = NULL)
    {
        parent::__construct(sprintf(self::CALL_TEMPLATE, $procedureName), $bindings, $db);
    } 
}