<?php
namespace Core\ClassSpawner\Iterable;
use Core\ClassSpawner\Spawner;

/**
 * Class factory produces array of class objects(entities) from raw array data
 * @author tarik
 */
class Factory extends Spawner implements \Core\Pattern\Iterable\Factory
{
    /**
     * Factory constructor.
     * @param string $spawnClass
     * @param \Traversable $dataSource
     */
    public function __construct(protected string $spawnClass, protected \Traversable $dataSource)
    {
    }

    public function __invoke():\Traversable
    {
        yield $this->spawn();
    }

	public function spawn():\Traversable
	{
		self::$classes[$this->spawnClass] ??= new \ReflectionClass($this->spawnClass);
        foreach ($this->dataSource as $row)
            yield $this->spawnObject(self::$classes[$this->spawnClass], $row);
	}
}