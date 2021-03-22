<?php 
namespace Core\Database\Oracle;
use Core\Database;
use Core\Database\Exception\Invalid;
use Core\Exception\Error;

/**
 * Oracle BLOB/CLOB support
 * @author tarik
 */
class Blob implements Database\Parameter
{
	/**
	 * Binding name template
	 * @var string
	 */
	const BIND_TEMPLATE = ":blob%s";
	
	/**
	 * Total cursors count to complete its naming
	 * @var int
	 */
	private static int $totalBlobs = 0;

	/**
	 * OCI BLOB
	 * @var \OCI_Lob
	 */
	private \OCI_Lob $blob;
	
	/**
	 * BLOB load flag
	 * @var bool
	 */
	private bool $bLoaded = false;

    /**
     * Blob constructor.
     * @param Connector|null $db
     * @param string|null $bindName
     * @param int $type
     * @throws Invalid
     * @throws Error
     */
	public function __construct(
	    private ?Connector $db=NULL,
        private ?string $bindName = NULL,
        private int $type = \OCI_B_BLOB)
	{
	    $this->bindName ??= sprintf(self::BIND_TEMPLATE, self::$totalBlobs++);
		$this->db       ??= \getApplication()->getDatabase();
		$this->blob     = oci_new_descriptor($this->db->getConnection()->getDescriptor(), \OCI_D_LOB);
        if (!$this->blob)
            throw new Invalid("Failed to create BLOB descriptor for {$this->bindName}.");
	}
	
	/**
	 *  Free cursor statement
	 */
	public function __destruct()
	{
	    if ($this->bLoaded)
	        $this->blob->close();
	}

	/**
	 * (non-PHPdoc)
	 * @see \Core\Database\Parameter::getValue()
	 */
	public function &getValue():mixed
	{
	   return $this->blob;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\Database\Parameter::getName()
	 */
	public function getName():string
	{
		return $this->bindName;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\Database\Parameter::getType()
	 */
	public function getType():int
	{
		return $this->type;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Core\Database\Parameter::getLength()
	 */
	public function getLength():int
	{
		return -1;
	}
}
