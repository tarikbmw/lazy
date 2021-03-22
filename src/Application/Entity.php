<?php
namespace Application;
use Core\Feature;
use Core\Feature\Export\Nodes;

/**
 * Entity class
 * @author tarik
 *
 */
class Entity implements Feature\Entity, Nodes
{
    /**
     * Entity name
     * @var string
     */
    private string $name;
    
    /**
     * Export attributes
     * @var array
     */
    private ?array $attributes;
    
    /**
     * Export nodes
     * @var array
     */
    private ?array $nodes;

    /**
     * @param string $name
     * @param array $attributes
     * @param array $nodes
     */
    public function __construct(string $name, ?array $attributes = NULL, ?array $nodes = NULL)
    {
        $this->name         = $name;
        $this->attributes   = $attributes;
        $this->nodes        = $nodes;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Feature\Exportable::getKey()
     */
    public function getKey():string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Feature\Export\Attributes::getAttributes()
     */
    public function getAttributes():?array
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Feature\Export\Nodes::getNodes()
     */
    public function getNodes():?array
    {
        return  $this->nodes;
    }
}