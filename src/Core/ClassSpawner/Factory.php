<?php
namespace Core\ClassSpawner;

/**
 * Class factory produces array of class objects(entities) from raw array data
 * @author tarik
 */
class Factory extends Spawner implements \Core\Pattern\Factory
{
    /**
     * Construct
     * @param string $spawnClass class name
     * @param array $arrayData source data to create class object
     */
    public function __construct(protected string $spawnClass, protected ?array $arrayData = [])
    {
    }

    public function __invoke():?array
    {
        return $this->spawn();
    }

	public function spawn():?array
	{

		if (!$this->arrayData)
			return NULL;

		self::$classes[$this->spawnClass] ??= new \ReflectionClass($this->spawnClass);
        $result = [];
        foreach ($this->arrayData as $row)
            $result[] = $this->spawnObject(self::$classes[$this->spawnClass], $row);

		return $result;
	}
}