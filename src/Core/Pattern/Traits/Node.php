<?php 
namespace Core\Pattern\Traits;
use Core\Exception\Error;

/**
 * Node default trait
 * @author tarik
 */
trait Node
{
    /**
     * Nodes collection
     * @var array
     */
    protected 	array	$nodes = [];
    
    /**
     * Attributes
     * @var array
     */
    protected    array   $attributes = [];
    
    /**
     * Export node name
     * @var string
     */
    protected   string  $key;
    
    /**
     * Get node name key
     * @return string
     */
    public function getKey():string
    {
        return $this->key;
    }
        
    /**
     * Get export nodes
     * @return array|NULL
     */
    public function getNodes():?array
    {
        return $this->nodes;
    }
    
    /**
     * Get export attributes
     * @return array|NULL
     */
    public function getAttributes():?array
    {
        return $this->attributes;
    }
    
    /**
     * Add child node
     * @param mixed $node
     */
    public function addNode($node)
    {
        $this->nodes[] = $node;
    }

    /**
     * Replace nodes
     * @param array $nodes
     */
    public function setNodes(array $nodes):void
    {
        $this->nodes = $nodes;
    }

    /**
     * Replace attributes
     * @param array $attributes
     */
    public function setAttributes(array $attributes):void
    {
        $this->attributes = $attributes;
    }

    /**
     * Add new array of attributes
     * @param array $attributes
     */
    public function addAttributes(array $attributes):void
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * Set attribute value by name
     * @param string $name
     * @param $value
     * @throws Error
     */
    public function setAttribute(string $name, $value):void
    {
        if (is_array($value) || is_object($value))
            throw new Error('Attribute value with a scalar type is required.');
        
        $this->attributes[$name] = $value;
    }

    /**
     * Set node key
     * @param string $name
     */
    public function setKey(string $name):void
    {
        $this->key = $name;
    }
}
