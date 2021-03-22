<?php
namespace Core\Database;
use Core\ClassSpawner;

/**
 * Trait Result
 * Working with query results or cursors
 * Creates emitter or factory which will produce enitity objects
 * @package Core\Database
 */
trait Result
{
    /**
     * Create emitter produces class object from raw array data
     * @param string $spawnClass
     * @return ClassSpawner\Emitter
     */
    public function createEmitter(string $spawnClass, ?callable $filter = NULL): ClassSpawner\Emitter
    {
        return $this->resultProducer($spawnClass, $filter, false, '\Emitter', 1 );
    }

    /**
     * Create factory produces array or iterator of class objects(entities) from raw array data
     * @param string $spawnClass
     * @param callable|null $filter
     * @param bool $iterable
     * @return ClassSpawner\Factory | ClassSpawner\Iterable\Factory
     */
    public function createFactory(string $spawnClass, ?callable $filter = NULL, bool $iterable = false):
    ClassSpawner\Factory | ClassSpawner\Iterable\Factory
    {
        return $this->resultProducer($spawnClass, $filter);
    }

    /**
     * Create emitter with entity which arguments is also entities too
     * @param string $spawnClass
     * @param callable|null $filter
     * @return ClassSpawner\MultipleEmitter
     */
    public function createMultipleEmitter(string $spawnClass, ?callable $filter = NULL): ClassSpawner\MultipleEmitter
    {
        return $this->resultProducer($spawnClass, $filter, false, '\MultipleEmitter', 1 );
    }

    /**
     * Create factory with entities which arguments is also entities too
     * @param string $spawnClass
     * @param callable|null $filter
     * @param bool $iterable
     * @return ClassSpawner\MultipleFactory | ClassSpawner\Iterable\MultipleFactory
     */
    public function createMultipleFactory(string $spawnClass, ?callable $filter = NULL, bool $iterable = false):
    ClassSpawner\MultipleFactory | ClassSpawner\Iterable\MultipleFactory
    {
        return $this->resultProducer($spawnClass, $filter, $iterable, '\MultipleFactory');
    }

    /**
     * Describes result
     */
    abstract public function dump(): string;

    /**
     * Filtering result to generator
     * @param callable $filter
     * @return \Traversable
     */
    private function filterIterable(callable $filter): \Traversable
    {
        foreach($this as $item)
            if ($filter($item))
                yield $item;
    }

    /**
     * Filtering result to array
     * @param callable $filter
     * @return array
     */
    private function filterArray(callable $filter):array
    {
        $result = [];
        foreach($this as $item)
            if ($filter($item))
                $result[] = $item;
        return $result;
    }

    /**
     * Create object for spawning entities with iterable result or as array
     * @param string        $spawnClass         this class name will be produced
     * @param callable|null $filter             filtering function
     * @param bool          $iterable           use object as iterable or array
     * @param string        $producerClassName  class name of produced emitter/factory
     * @param int           $type               type of produced object 0 - factory or 1 - emitter, emitter couldn't be iterable
     * @return mixed
     */
    protected function resultProducer( string $spawnClass, ?callable $filter, bool $iterable = false, string $producerClassName = '\Factory', int $type = 0):mixed
    {
        // emitter couldn't be iterable
        $iterable = $type ? false : $iterable;
        $className = '\Core\ClassSpawner' . ($iterable ? '\Iterable' : ''). $producerClassName;

        // With non iterable objects we'll fetch them as array
        if (!$iterable)
        {
            $array = [];

            /**
             * If we use filtering handler, it will fetch object as iterator
             * So, we do not need to fetch object as array
             */
            if (!$filter)
                foreach($this as $item)
                    $array[] = $item;

            // For emitter we'll use only first element of result
            if ($type)
            {
                $array[0] ??= NULL;
                return new $className($spawnClass, $filter ? $this->filterArray($filter) : $array[0]);
            }
            return new $className($spawnClass, $filter ? $this->filterArray($filter) : $array);
        }
        return new $className($spawnClass, $filter ? $this->filterIterable($filter) : $this);
    }
}