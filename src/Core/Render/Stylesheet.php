<?php 
namespace Core\Render;
use Core\Setup;

/**
 * Class describes stylesheet document for XML processing
 * @author tarik
 *
 */
abstract class Stylesheet
{
	/**
	 * 
	 * Template file URI
	 * @var string
	 */
	protected string $URI;
	
	/**
	 * 
	 * Full system path to template file 
	 * @var string
	 */
	protected string $path;
	
	/**
	 * 
	 * Constuctor
	 * @param string $filename - Stylesheet file name (only file name, with out layout path)
	 */
	public function __construct(string $filename)
	{
		$config 	= Setup::getInstance()->getStylesheet();

		$this->path = sprintf("%s/%s", $config->path, $filename);
		$this->URI	= sprintf("%s/%s%s", $config->uri, $filename, $config->version);
	}

	/**
	 * Assign stylesheet to document
	 * @param \DOMDocument $target - target document
	 */
	abstract public function assign(\DOMDocument $target):void;

    /**
     * Get stylesheet path
     * @return string
     */
	public function getPath():string
	{
		return $this->path;
	}

    /**
     * Get stylesheet URI
     * @return string
     */
	public function getURI():string
	{
		return $this->URI;
	}
}