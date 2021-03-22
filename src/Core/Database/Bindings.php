<?php 
namespace Core\Database;

/**
 * Query bindings
 * @author tarik
 */
class Bindings extends \ArrayObject
{
    /**
     * Bindings constructor.
     * @param array $bindings array of bindings
     */
	public function __construct(private array $bindings = [])
	{		
		parent::__construct($this->bindings, \ArrayObject::ARRAY_AS_PROPS);
	}	
	
	/**
	 * Set binding by name
	 * @param string $name - name of parameter
	 * @param mixed $value - value to set
	 */
	public function __set(string $name, mixed $value):void
	{
		$this[$name] = $value;
	}
}