<?php
namespace Core\ClassSpawner;

/**
 * Class Parameter
 * @package Core\ClassSpawner
 */
class Parameter
{
    /**
     * Parameter constructor.
     * @param string    $name
     * @param string    $class
     * @param int       $position
     */
    public function __construct(    protected string    $name,
                                    protected string    $class,
                                    protected int       $position)
    {
    }
    
    public function __invoke():array
    {
        return
        [
            'name'		=> $this->name,
            'class' 	=> $this->class,
            'position'	=> $this->position
        ];
    }
}