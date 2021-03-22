<?php
namespace Application\Access;
use Core\Exception\Error;

/**
 * For registered user entry owner only rule
 * @author tarik
 *
 */
trait Owner
{
    use Registered;
    
    /**
     * Check rule
     * @return boolean
     */
    function accessible():bool
    {
        return parent::accessible() && $this->checkOwner($this->getVariable());
    }

    /**
     * Check for owner rule
     * Check the pair user id and some variable, return true if they does matched
     * @param int $variable
     */
    protected abstract function checkOwner(int $variable):bool;

    /**
     * Get variable value to check owner
     */
    protected abstract  function getVariable():int;

    /**
     * Process rejection of rule
     * @throws Error
     */
    function rejection():void
    {
        throw new Error("You must be an authorized user and to be an owner to access this entry.");
    }
}