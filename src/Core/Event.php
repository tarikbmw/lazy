<?php
namespace Core;

/**
 * Event class
 * @author tarik
 */
class Event 
{
    /**
     * Event constructor.
     * @param string $name      event name
     * @param string $method    request method (POST/GET)
     */
	function __construct(protected string $name, protected string $method)
	{
	}

    /**
     * Get event name
     * @return string
     */
	public function getName():string
	{
		return $this->name;
	}


    /**
     * Get request method for event
     * @return string
     */
	public function getMethod():string
	{
		return $this->method;
	}
}