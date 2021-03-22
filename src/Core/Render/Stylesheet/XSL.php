<?php 
namespace Core\Render\Stylesheet;

/**
 * XSL stylesheet class, assign it to document
 * @author tarik
 */
class XSL extends \Core\Render\Stylesheet
{
	const STYLESHEET_TYPE          = 'text/xsl';
	const STYLESHEET_FOLDER        = 'template';	
	const STYLESHEET_PATH_PATTERN  = "%s/%s/%s?%s";

    /**
     * Processing instruction
     * @var \DOMNode|null
     */
	private static ?\DOMNode $stylesheetPI = NULL;

    /**
     * XSL constructor.
     * @param string $filename
     * @throws \Exception
     */
	public function __construct(string $filename)
	{	    
		$config 	= \getApplication()->getConfig()->getStylesheet();
		
		$this->path = sprintf(self::STYLESHEET_PATH_PATTERN, $config->path, self::STYLESHEET_FOLDER, $filename,$config->version);
		$this->URI	= sprintf(self::STYLESHEET_PATH_PATTERN, $config->uri, self::STYLESHEET_FOLDER, $filename, $config->version);
	}

	/**
	 * Assign stylesheet to document 
	 * @see Core\Stylesheet::assign()
	 * @param \DOMDocument $target
	 */
	public function assign(\DOMDocument $target):void
	{
		$hdr = sprintf("type=\"%s\" href=\"%s\"", self::STYLESHEET_TYPE, $this->getURI());

		if (self::$stylesheetPI)
        {
            self::$stylesheetPI->data = $hdr;
            return;
        }

        self::$stylesheetPI = $target->createProcessingInstruction('xml-stylesheet', $hdr);

        if ($target->documentElement instanceof \DOMNode)
        {
            $target->insertBefore(self::$stylesheetPI, $target->documentElement);
            return;
        }

        $target->appendChild(self::$stylesheetPI);
	}
}