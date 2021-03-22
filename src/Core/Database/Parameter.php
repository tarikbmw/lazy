<?php
namespace Core\Database;

/**
 * Request parameter
 * @author tarik
 */
interface Parameter
{
    /**
     * Get name
     * @return string
     */
    public function getName():string;

    /**
     * Get value
     * @return mixed
     */
    public function &getValue():mixed;

    /**
     * Get type for binding
     * @return int
     */
    public function getType():int;

    /**
     * Get length
     * @return int
     */
    public function getLength():int;
}
