<?php
namespace Module;
use Core\Database\MySql\Query;
use Module\Authentication\Entity\Account;
use Core\Crypt;

/**
 * Class Authentication
 * Demo module
 * @package Module
 */
class Authentication extends \Core\Module
{
    use \Core\Pattern\Traits\Singleton; // Use if for singleton pattern

    /**
     * Current user account
     * @var mixed|Account|null
     */
	private ?Account $currentUser = NULL;

	protected function __construct()
	{
		$this->setKey('authentication');

		if (isset($_SESSION['currentUser']) && ($_SESSION['currentUser'] instanceof Account))
		    $this->currentUser = $_SESSION['currentUser'];
	}

	/**
	 * Check user login
	 * @return boolean
	 */
	public function isLoggedIn():bool
	{
		return $this->getUser() instanceof Account;
	}
	
	/**
	 * Get current user
	 * @return Account
	 */
	public function getUser():?Account
	{
		if ($this->currentUser instanceof Account)
			return $this->currentUser;
		
		if (!isset($_COOKIE['sessionData']))
			return NULL;

	    $data = isset($_COOKIE['sessionData']) ? $this->getCrypt()->decode($_COOKIE['sessionData']) : NULL;
        $userID = $this->currentUser->id ?? ($data ? $data['id'] : NULL);
        if (!$userID)
            return NULL;

        return  $_SESSION['currentUser'] = $this->currentUser =  $this->getUserByID($userID);
	}

    /**
     * Get user profile by id
     * @param int $id
     * @return Account|null
     */
	public function getUserByID(int $id):?Account
	{
	    $query 				= new Query("select * from `userList` where `userID`=?");
	       $query->userID 		= $id;
	    return $query->createMultipleEmitter('\Module\Authentication\Entity\Account')->spawn();
	}

	/**
	 * Setup current user profile
	 * @param Account $user
	 */
	public function setUser(?Account $user):void
	{
		$_SESSION['currentUser'] = $this->currentUser = $user;	
	}

	/**
	 * Get Crypt instance for cookie encryption/decryption
	 * @return Crypt
	 */
	public function getCrypt():Crypt
	{
	    return new Crypt(\getApplication()->getConfig()->getSession()->key);
	}
}