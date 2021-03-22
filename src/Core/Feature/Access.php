<?php
namespace Core\Feature;

/**
 * Access property interface
 * @author tarik
 *
 */
interface Access
{
    /**
     * Check rule
     * @return bool
     */
    function accessible():bool;

    /**
     * Process rule acception
     */
    function acception():void;

    /**
     * Process rule rejection
     */
    function rejection():void;
}