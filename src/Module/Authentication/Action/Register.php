<?php
namespace Module\Authentication\Action;
use Application\Response\Accepted;
use Core\Database\MySql\Query;
use Core\Event;
use Exception;

/**
 * Class Register
 * @package Module\Authentication\Action
 */
class Register extends Event\Action
{
    /**
     * @param string|null $Login
     * @param string|null $Password
     * @param string|null $Password2
     * @throws \Core\Exception\Error
     */
	public function __invoke(?string $Login=NULL, ?string $Password=NULL, ?string $Password2 = NULL)
	{	
	    if (!$Login)
	        throw new Exception('Login missed');
	    
        preg_match('/^[A-z0-9._%+-]+@[A-z0-9.-]+\.[A-z]{2,4}$/', $Login) or
            throw new Exception('Bad email.');
	        
        $Login = trim(strtolower($Login));

        preg_match('/^[A-z0-9\-_]{6,16}$/', $Password) or
            throw new Exception('Bad password, use 6-16 characters and numbers.');

        if ($Password != $Password2)
            throw new Exception('Password does not match with retyped password.');


		$query = new Query( "insert into account(email, password) values (?, ?)");
            $query->Login 		= $Login;
            $query->Password 	= hash('sha256',$Password, true);
		$query();

        new Accepted($this, 'You are successfully registered, now you may login.', '/');
	} 
}