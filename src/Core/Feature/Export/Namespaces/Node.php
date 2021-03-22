<?php
namespace Core\Feature\Export\Namespaces;

/**
 * Export element namespace attribute name
 * @author tarik
 */
interface Node
{
	public function getNodeNamespace():?string;
}