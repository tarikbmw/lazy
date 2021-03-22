<?php
namespace Core\Feature\Export\Namespaces;

/**
 * Export element attribute namespace name 
 * @author tarik
 */
interface Attribute
{
	public function getAttributesNamespace():?string;
}