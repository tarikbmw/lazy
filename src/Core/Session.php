<?php
namespace Core;
use \Core\Exception\Error;
use \Core\Pattern\Singleton;

/**
 * Session suport class
 * @author tarik
 *
 */
class Session implements Singleton
{
	private static 		$Instance;
	
	/**
	 * Session ID
	 * @var string
	 */ 
	private string $id;
	
	/**
	 * Creates session instance
	 * @param string $sessionID - exist or new to set session ID
	 * @return \Core\Session
	 */
	public static function getInstance(?string $sessionID = NULL):self
	{
        return static::$Instance ?? (static::$Instance = new static($sessionID));
	}

	
	/**
	 * Construct
	 * @param string $sessionID
	 * @throws Error
	 */
	protected function __construct(?string $sessionID = NULL)
	{
	    if (session_status() == PHP_SESSION_ACTIVE)
	        return;

        $this->id = $sessionID ?? $this->generateUUID();
        session_id($this->id);

		if (!session_start())
			throw new Error("Could not start session.");


	}
	
	public function close()
	{
	    if (session_status() != PHP_SESSION_ACTIVE)
	        return;
	    
	    if (!session_write_close())
	        throw new Error('Could not commit session.');
	}
	
	/**
	 * Get session ID
	 * @return string
	 */
	public function GetID():string
	{
		return $this->id;
	}

	private function generateUUID():string
    {
        //$format = '%04x%04x-%04x-%04x-%04x-%04x%04x%04x';
        $format = '%04x%04x%04x%04x%04x%04x%04x%04x';

        $generator = fn() => mt_rand(0, 0xffff);

        return strtoupper(sprintf($format, $generator(), $generator(), $generator(), $generator() | 0x4000, $generator() | 0x8000, $generator(), $generator(), $generator()));
    }
}
