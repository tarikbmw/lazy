<?php
namespace Core\Feature;

/**
 * Object export feature
 * 
 * @author tarik
 *
 */
interface Exportable
{
    /**
     * Return object's node key
     */
	public function getKey():string;
}