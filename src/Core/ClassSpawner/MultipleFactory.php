<?php
namespace Core\ClassSpawner;

/**
 * Creates factory with entities which arguments is also entities too
 * @author tarik
 */
class MultipleFactory extends Factory
{
	public function spawn():?array
	{		
		if (empty($this->arrayData))
			return NULL;
	
        	$this->arrangeArgumentsAsObjects(self::$classes[$this->spawnClass] ??= new \ReflectionClass($this->spawnClass), $this->spawnClass);

		foreach ($this->objects as $key=>$object)
			foreach($this->arrayData as $arrayKey=>$arrayValue)		
			    $this->arrayData[$arrayKey][$key] = 
                    $this->spawnObject(self::$classes[$object] ??= new \ReflectionClass($object), $this->arrayData[$arrayKey]);
        
        	$result = [];
		foreach($this->arrayData as $arrayKey=>$arrayValue)
		    $result[] = $this->spawnObject(self::$classes[$this->spawnClass], $arrayValue);
			
		return $result;
	}
}
