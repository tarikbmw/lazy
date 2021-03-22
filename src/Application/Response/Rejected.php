<?php
namespace Application\Response;

/**
 * Rejection of the bad request, or some error occurs while request was processed
 */
class Rejected extends Accepted
{
    public function __destruct() 
    {
        $this->owner->setAttribute('response', 'rejected');
    }
}