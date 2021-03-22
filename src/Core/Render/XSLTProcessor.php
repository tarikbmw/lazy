<?php
namespace Core\Render;
use Core\Render\Stylesheet\XSL;
use Core\Exception\Error;

/**
 * Server side XSLT processing
 * @author tarik
 */
class XSLTProcessor extends XML
{
	/**
	 * XSTL Processor for server side processing
	 * @var \XSLTProcessor
	 */
	protected ?\XSLTProcessor $processor = null;

    /**
     * Rendered output
     * @var string
     */
	protected string $output = '';

    /**
     * XSLTProcessor constructor.
     * @param array|null $nodes
     * @throws Error
     */
	public function __construct(protected array $nodes = [])
	{
	    parent::__construct($nodes);

	    // Set session cookie for server side processing
	    $cookie = sprintf("Cookie: %s=%s\r\n",\getApplication()->getConfig()->getSession()->name, \getApplication()->getSession()->GetID());
        $opt =
        [
            'http'=>
            [
	            'method'=>"GET",
	            'header'=> $cookie
	        ]
	    ];

        // Add cookie to server side XML processor stream context
        $context = stream_context_create($opt);
        libxml_set_streams_context($context);
	}

    /**
     * Get current XSLTProcessor or creates new one
     * @return \XSLTProcessor
     */
	public function getProcessor():\XSLTProcessor
	{
    	return $this->processor = ($this->processor instanceof \XSLTProcessor) ? $this->processor : new \XSLTProcessor;
	}

    /**
     * Adding XSL stylesheet to the document
     * @param \Core\Render\Stylesheet $style
     */
	public function setStylesheet(Stylesheet $style):void
	{
        if (!($style instanceof XSL))
            return;

        ($xsl = new \DOMDocument())->load($style->getURI());
        $this->getProcessor()->importStylesheet($xsl);
	}

    /**
     * Process render
     * @return string|null
     * @throws Error
     */
	public function process():?string
	{
	    // Close session before render
	    \getApplication()->getSession()->close();

	    if ($this->bProcessDocumentOnly)
	        return $this->output = $this->getProcessor()->transformToXml($this->getDocument()) ?? $this->getDocument()->saveXML();

        if (!empty($this->nodes))
            foreach ($this->nodes as $dataKey=>$dataValue)
                    $this->processNode($dataKey, $dataValue, $this->getDocument()->documentElement ? $this->getDocument()->documentElement : $this->getDocument());

        if (!($this->getDocument() instanceof \DOMDocument))
            throw new \Exception('Failed to process empty document.');

        return $this->output =  $this->getProcessor()->transformToXml($this->getDocument()) ?? $this->getDocument()->saveXML();
	}

    /**
     * Return rendered output
     * @return string
     */
	public function __toString():string
	{
        return $this->output;
	}
}