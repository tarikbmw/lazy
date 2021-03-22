<?php
namespace Core\ClassSpawner;


/**
 * Creates emitter with entity which arguments is also entities too
 * @author tarik
 */
class MultipleEmitter extends Emitter
{
	protected array $objects = [];
	
	public function spawn():?object
	{		
		if (!$this->arrayData)
			return NULL;

		$this->arrangeArgumentsAsObjects(self::$classes[$this->spawnClass] ??= new \ReflectionClass($this->spawnClass), $this->spawnClass);
		foreach ($this->objects as $key=>$object)
			$this->arrayData[$key] = $this->spawnObject(self::$classes[$object] ??= new \ReflectionClass($object), $this->arrayData);

		return $this->spawnObject(self::$classes[$this->spawnClass], $this->arrayData);
	}

}