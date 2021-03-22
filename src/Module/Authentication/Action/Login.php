<?php
namespace Module\Authentication\Action;
use Core\Event;
use Core\Database\MySql\Query;
use Application\Response\Accepted;
use Application\Response\Rejected;
use Exception;

/**
 * Class Login
 * @package Module\Authentication\Action
 */
class Login extends Event\Action
{
    /**
     * @param string|null $Login        user email
     * @param string|null $Password     user password
     * @throws \Core\Exception\Error
     * @throws \ReflectionException
     */
	public function __invoke(?string $Login = NULL, ?string $Password = NULL)
	{
        if (!$Login)
            throw new Exception('Login missed.');

        if (!$Password)
            throw new Exception('Password missed.');

        $proc = new Query('select * from account where email = ? and password = ?');
            $proc->login = $Login;
            $proc->password = hash('sha256',$Password, true);
        $profile = $proc->createMultipleEmitter('\Module\Authentication\Entity\Account')->spawn() or
            throw new Exception('Wrong password or no user found.');
        $this->getOwner()->setUser($profile);

        $redirect = '/';
        if (isset($_COOKIE['referer']))
        {
            $redirect = $_COOKIE['referer'];
            setcookie('referer', NULL, time() - 3600);
        }

        new Accepted($this, 'Welcome to LazyFramework demo.', $redirect);
    }
}