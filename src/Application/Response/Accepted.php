<?php
namespace Application\Response;
use Core\Event\Action;
use Core\Module;

/**
 * Successfull response
 */
class Accepted implements \Application\Response
{
    /**
     * Response owner
     * @var Module
     */
    protected  Module $owner;
    
    /**
     * Response for an accepted request constructor.
     * @param Action        $action         response sender, response will be attached to action's owner
     * @param string|null   $message        message text
     * @param string|null   $redirect       redirect url
     * @param array|null    $attributes     array of attributes
     * @param array|null    $nodes          array of child nodes
     * @throws \Core\Exception\Error
     */
    public function __construct(Action $action, ?string $message = NULL, ?string $redirect = NULL, ?array $attributes = NULL, ?array $nodes = NULL)
    {
        $this->owner = $action->getOwner();
        
        if ($attributes)
            $this->owner->setAttributes($attributes);
        
        if ($nodes)
            $this->owner->setNodes($nodes);
        
        if ($message)
            $this->owner->setAttribute('message', $message);
        
        if ($redirect)
            $this->owner->setAttribute('redirect', $redirect);
    }    
    
    public function __destruct() 
    {
        /**
         * Rewrite response type attribute
         */
        $this->owner->setAttribute('response', 'accepted');
    }
}