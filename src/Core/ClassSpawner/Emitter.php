<?php
namespace Core\ClassSpawner;

/**
 * Class emitter produces class object from raw array data
 * @author tarik
 */
class Emitter extends Spawner implements \Core\Pattern\Emitter
{
	/**
	 * Construct
	 * @param string $spawnClass class name
	 * @param array $arrayData source data to create class object
	 */
	public function __construct(protected string $spawnClass, protected ?array $arrayData = [])
	{
	}

    /**
     * @return object|null
     */
	public function __invoke():?object
	{
		return $this->spawn();
	}

    /**
     * Spawn class object
     * @return object|null
     * @throws ReflectionException
     */
	public function spawn():?object
	{		
		return !$this->arrayData ? NULL :
            $this->spawnObject(self::$classes[$this->spawnClass] ??= new \ReflectionClass($this->spawnClass),
                        $this->arrayData);
	}
}