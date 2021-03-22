<?php
namespace Core\Pattern;
use Core\Feature\Exportable;
use Core\Feature\Entity;

/**
 * Collection pattern, to work with entities
 * 
 * @author tarik
 *
 */
interface Collection extends Exportable
{
    /**
     * Adding entity to collection
     * @param array $entity
     */
	public function addEntity(array $entity):void;
	
	/**
	 * Adding list of entities
	 * @param array [Entity $entities]
	 */
	public function addEntities(...$entities):void;
	
	/**
	 * Export all entities
	 * @return array|NULL
	 */
	public function export():?array;
}

