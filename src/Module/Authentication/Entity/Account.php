<?php
namespace Module\Authentication\Entity;
use Core\Pattern\Traits\Entity;

class Account implements \Core\Feature\Entity
{
    use Entity;
    
    /**
     * Entity name to export
     * @var string
     */
    const ENTITY_NAME = 'account';
    
    public 		?int $id;
    public		?string $mail;
    private		?string $password;


    /**
     * Account constructor.
     * @param int|null $accountID   account identifier
     * @param string|null $password password string
     * @param string|null $email    email, used as login
     */
    public function __construct(?int $accountID=NULL, ?string $password=NULL, ?string $email=NULL)
    {
        $this->id  		= $accountID;
        $this->password = $password;
        $this->mail 	= $email;
    }
    
    /**
     * Get user ID
     * @return int|NULL
     */
    public function getID():?int
    {
        return $this->id;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Feature\Export\Attributes::getAttributes()
     */
    public function getAttributes():?array
    {
        return array(
            "accountID"	=>	$this->id,
            "mail" 		=> 	$this->mail,
        );
    }
}