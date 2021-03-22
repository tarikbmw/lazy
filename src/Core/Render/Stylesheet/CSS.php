<?php 
namespace Core\Render\Stylesheet;
use Core\Setup;

/**
 * CSS stylesheet for server processing
 * @author tarik
 */
class CSS extends \Core\Render\Stylesheet
{
	public function __construct(string $filename)
	{
		
		$config 	= Setup::getInstance()->getStylesheet();
		
		$this->path = "{$config->path}/css/$filename";
		$this->URI	= "{$config->uri}/css/$filename";
	}	
	
	/**
	 * Assign stylesheet to document 
	 * @see Core\Stylesheet::assign()
	 * @param \DOMDocument $target
	 */
	public function assign(\DOMDocument $target):void
	{
		if ($target->documentElement instanceof \DOMNode)
			$target->insertBefore($target->createProcessingInstruction('xml-stylesheet', "type=\"text/css\" href=\"{$this->getURI()}\""), $target->documentElement);
		else
		    $target->appendChild($target->createProcessingInstruction('xml-stylesheet', "type=\"text/css\" href=\"{$this->getURI()}\""));
	}
}