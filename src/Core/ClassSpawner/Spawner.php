<?php
namespace Core\ClassSpawner;

/**
 * Class Spawner
 * @package Core\ClassSpawner
 */
class Spawner
{
    /**
     * Global cache of reflection objects
     * @var array
     */
    protected static array $classes = [];

    /**
     * Spawn object by reflection class with parameters
     * @param \ReflectionClass $class
     * @param array $param
     * @return object
     * @throws \Exception
     */
    protected function spawnObject(\ReflectionClass $class, array $param):object
    {
        return $class->newInstanceArgs(
            $this->arrangeFunctionArgumentsByName($class->getConstructor(), $param));
    }

    /**
     * Rearrange array data as function argument count
     * @param \ReflectionFunctionAbstract $function
     * @param array $input
     * @return array
     * @throws \Exception
     */
    protected function arrangeFunctionArgumentsByName(\ReflectionFunctionAbstract $function, array $input):array
    {
        $arguments = [];
        foreach($function->getParameters() as $parameter)
            $arguments[$parameter->getPosition()] = $input[$parameter->getName()] ??
                ($parameter->isDefaultValueAvailable() ?
                    $parameter->getDefaultValue() : NULL);

        return $arguments;
    }

    /**
     * Explore class parameters which is instance of object recursively and sort them to spawn origin class
     * @param \ReflectionClass $class - origin class to spawn
     * @param string $objectName
     * @return mixed
     * @throws \ReflectionException
     */
    protected function arrangeArgumentsAsObjects(\ReflectionClass $class, string $objectName)
    {
        if (isset($this->objects[$objectName]))
            return $this->objects[$objectName];

        $parameters = $class->getConstructor()?->getParameters();
        foreach($parameters as $parameter)
            if ($this->getClass($parameter))
                $this->arrangeArgumentsAsObjects($this->getClass($parameter), $parameter->getName());

        $this->objects[$objectName] = $class->getName();
    }

    /**
     * Get parameter class, using this instead depricated method
     * @param \ReflectionParameter $parameter
     * @return \ReflectionClass|null
     * @throws \ReflectionException
     */
    private function getClass(\ReflectionParameter $parameter):?\ReflectionClass
    {
        $type = $parameter->getType();
        if (!$type || $type->isBuiltin())
            return NULL;

        $class = $type->getName();

        if(!class_exists($class))
            return NULL;

        return self::$classes[$class] ??= new \ReflectionClass($class);
    }
}