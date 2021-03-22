<?php
namespace Core\Database\MySql;
use Core\Database\Exception;

class Query extends \Core\Database\Query
{
    use Binding;
 
    static ?\ReflectionClass $refStatement = NULL;
    private int $position;
    private bool $bExecuted = false;

    public function execute():?array
    {
        /**
         * Inbound types
         * @var string
         */
        $typesIn 			= '';
        
        /**
         * Inbound parameters
         * @var array
         */
        $parametersIn 		= [];
        
        /**
         * Result row names
         * @var array
         */
        $resultRow			= [];
        
        /**
         * Result values
         * @var array
         */
        $parametersOut 		= [];
        
        /**
         * Final result output
         * @var array
         */
        $output 			= [];

        /**
         * Hashed key stored in cache for this result
         * @var string
         */
        $cachedKey = '';

        /**
         * Use cache?
         */
        if ($this->cache && !$this->cacheFlush)
        {
            $val = '';

            foreach ($this->bindings as $k=>$v)
                $val .= $k. $v;

            $cachedKey      = md5($this->getQuery()).md5($val);
            $cachedResult   = $this->cache->get($cachedKey);
            if ($cachedResult)
            {
                $this->cacheFlush = false;
                return $this->result = $cachedResult;
            }
        }

        if ($this->bExecuted)
            return $this->result;

        // Get the reflection of mysql statement
        if (!self::$refStatement)
            self::$refStatement = new \ReflectionClass("mysqli_stmt");

        if ($this->bindings->count())
        {
            // Get binding types and parameters
            foreach ($this->bindings as $parameter)
            {
                $typesIn 			.= 	$this->getType($parameter);
                $parametersIn[] 	= 	$parameter;
            }
            
            // Move the types string for the first element of parameters array
            array_unshift($parametersIn, $typesIn);
            
            // Create pointers for parameters value
            $bindParamIn = [];
            foreach ($parametersIn as $k=>$i)
                $bindParamIn[$k] = &$parametersIn[$k];
                
            // Bind the parameters
            $bind_param 	= self::$refStatement->getMethod("bind_param");
            $bind_param->invokeArgs($this->statement, $bindParamIn);
        }
        
	    $this->statement->execute() or throw new Exception\Query($this->db->getConnection()->error);
            
            
        // Get the result col names
        $metadata 	= $this->statement->result_metadata();
        if (!$metadata)
            return NULL;
            
        $fields 	= $metadata->fetch_fields();
        
        foreach ($fields as $field)
            $resultRow[] = &$parametersOut[$field->name];
            
        // Bind the result
        $bind_result = self::$refStatement->getMethod("bind_result");
        $bind_result->invokeArgs($this->statement, $resultRow);
        
        // Fetching the result
        for($i=0; $this->statement->fetch(); $i++)
        {
            $output[$i] = [];
            foreach($parametersOut as $key=>$value)
                $output[$i][$key] = $value;
        }
        
        $this->statement->close();

        // Store result to cache
        if ($this->cache)
            $this->cache->set($cachedKey, $output, $this->cacheExpired);

        $this->bExecuted = true;
        return $this->result = $output;
    }

    public function dump():string
    {
	    return print_r($this->result, true);
    }

    public function current():mixed
    {
        return $this->result[$this->position];
    }

    public function next():void
    {
        $this->position++;
    }

    public function key():int
    {
        return $this->position;
    }

    public function valid():bool
    {
        if (!$this->result)
            $this->execute();

        return isset($this->result[$this->position]);
    }

    public function rewind():void
    {
        $this->execute();
        $this->position = 0;
    }
}
