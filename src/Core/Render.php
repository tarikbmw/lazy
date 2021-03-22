<?php
namespace Core;

/**
 * Renders output data from data nodes
 * @author tarik
 */
abstract class Render
{
    /**
     * Render constructor.
     * @param array|null $nodes
     */
	public function __construct(protected array $nodes = [])
	{
	}

    /**
     * Replace current nodes by new set
     * @param array $nodes
     */
	public function setNodes(array $nodes):void
	{
		$this->nodes = $nodes;
	}

    /**
     * Add new node
     * @param $node
     */
	public function addNode(mixed $node):void
	{
	    $this->nodes[] = $node;
	}

    /**
     * Add or replace new node by key
     * @param string $key
     * @param $node
     */
	public function setNode(string $key, mixed $node):void
	{
		$this->nodes[$key] = $node;
	}

    /**
     * Magic method for easy set node
     * @param string $key
     * @param $node
     */
	public function __set(string $key, mixed $node):void
	{
		$this->setNode($key, $node);
	}

    /**
     * Magic method for easy get node
     * @param string $key
     * @return mixed|null
     */
	public function __get(string $key):mixed
	{
		return $this->nodes[$key] ?? NULL;
	}

    /**
     * Set render stylesheet (for XML)
     * @param Render\Stylesheet $style
     */
    abstract public function setStylesheet(Render\Stylesheet $style):void;

    /**
     * Rendering process
     * @return string|null
     */
	abstract public function process():?string;

    /**
     * Return rendered output as string
     * @return string
     */
	abstract public function __toString():string;
}