<?php 
namespace Core\Database\Oracle;
use Core\Feature\Exportable;
use Core\Feature\Export;

/**
 * Class Parameter
 * @package Core\Database\Oracle
 */
class Parameter implements \Core\Database\Parameter, Exportable, Export\Attributes, Export\Text
{
    /**
     * Parameter constructor.
     * @param string $name
     * @param mixed $value
     * @param int $type
     * @param int $length
     * @param string $title
     */
	public function __construct(
	    private string  $name,
        private mixed   $value,
        private int     $type=\SQLT_CHR,
        private int     $length=-1,
        public string   $title = '')
	{
		return $this;
	}

    /**
     * Get parameter name
     * @return string
     */
	public function getName():string
	{
		return $this->name;
	}

    /**
     * Get value
     * @return mixed
     */
	public function &getValue():mixed
	{
		return $this->value;
	}

    /**
     * Get type
     * @return int
     */
	public function getType():int
	{
		return $this->type;
	}

    /**
     * Get length
     * @return int
     */
	public function getLength():int
	{
		return $this->length;
	}

    /**
     * Export attributes
     * @return string[]|null
     */
	public function getAttributes():?array
	{
		return ['name'=>$this->title];
	}

    /**
     * Export key
     * @return string
     */
	public function getKey():string
	{
		return 'parameter';
	}

    /**
     * Export text
     * @return array|null
     */
	public function getText():?array
    {
        return $this->value;
	}
}