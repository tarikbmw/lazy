<?php
namespace Core\Render;
use Core\Feature\Export\Fragment;
use Core\Exception\Error;
use Core\Feature\Exportable;
use Core\Feature\Export\Attributes;
use Core\Feature\Export\Nodes;
use Core\Feature\Export\Text;
use Core\Feature\Export\Namespaces;
use Core\Render\Stylesheet\XSL;

/**
 * XML Render, creates XML document from array of Entities or Exportable objects
 * @author tarik
 */
class XML extends \Core\Render
{
	/**
	 * Main document, element acceptor
	 * @var ?\DOMDocument
	 */
	private ?\DOMDocument $document = null;
	
	/**
	 * Do not import data, process document instance only.
	 * This flag is under setDocument control.
	 * @var boolean
	 */
	private bool $bProcessDocumentOnly = false;
	
	/**
	 * Output
	 * @var string
	 */
	protected string $xml = '';

	/**
	 * Set XSL stylesheet to the document by URL
	 * @param string $url - URL starts from template path defined in configuration file
	 */
	public function setStylesheetURL(string $url):void
	{
	    $this->setStylesheet(new XSL($url));
	}

    /**
     * Assign stylesheet to the document
     * @param \Core\Render\Stylesheet $style
     */
	public function setStylesheet(Stylesheet $style):void
	{
		$style->assign($this->getDocument());
	}

    /**
     * Get curretn DOM document or create new one
     * @return \DOMDocument
     */
	public function getDocument():\DOMDocument
	{
		if (($this->document instanceof \DOMDocument) || ($this->document instanceof \DOMDocumentFragment))
			return $this->document;
			
		$this->document = new \DOMDocument("1.0", "UTF-8");
		
		$this->document->preserveWhiteSpace = true;
		$this->document->formatOutput = true;

		return $this->document;
	}

    /**
     * Process render
     * @return string|null
     * @throws Error
     */
	public function process():?string
	{
	    if ($this->xml)
	        return $this->xml;

	    if ($this->bProcessDocumentOnly)
	        return $this->xml = $this->getDocument()->saveXML();
	         
        if (!empty($this->nodes))
            foreach ($this->nodes as $dataKey=>$dataValue)            
                    $this->processNode($dataKey, $dataValue, $this->getDocument()->documentElement ?? $this->getDocument());

        return $this->xml =  $this->getDocument()->saveXML();	    
	}

    /**
     * retirn output buffer
     * @return string
     */
	public function __toString():string
	{
        return $this->xml;
	}

    /**
     * Replace document
     * @param \DOMDocument $document
     */
	public function setDocument(\DOMDocument $document):void
	{
		$this->document = $document;
		$this->bProcessDocumentOnly = true;
	}

    /**
     * Add document
     * @param \DOMDocument $document
     */
	public function addDocument(\DOMDocument $document):void
	{
		if ($this->getDocument()->documentElement instanceof \DOMElement)
		{
		    $this->getDocument()->replaceChild($document->documentElement, $this->getDocument()->documentElement);
		    return;
		}
		
		$element = $this->getDocument()->importNode($document->documentElement);
		if ($element instanceof \DOMElement)
			$this->getDocument()->appendChild($element);
	}

    /**
     * Import node to render
     * Creates DOMElement from node
     * @param string $key
     * @param mixed $node
     * @return \DOMElement
     * @throws Error
     */
	protected function importNode(string $key, mixed $node):\DOMElement
	{	    	    
		if (!$node)
		{    
		    if (is_numeric($key))
		        throw new \Core\Exception\Error("Could not import Node with numeric key `$key` with NULL value.");
		        
		    return $this->document->createElement($key);
		}

		if (is_int($node) || is_bool($node) || is_double($node) || 
		    is_float($node) || is_string($node))			
			return $this->document->createElement($key, $node);
			
		throw new \Core\Exception\Error("Could not import Node with such type `$key` => `".print_r($node,true)."`.");
	}

    /**
     * Parse nodes to XML document
     * @param string|null $key
     * @param mixed $node               current node Key
     * @param mixed $rootElement        DOMDocument root element
     * @throws Error
     */
	protected function processNode(?string $key, mixed $node, mixed $rootElement):void
	{
		if ($node instanceof Exportable)
		{
		    if ($node instanceof Namespaces\Node)
		        $element = $this->getDocument()->createElementNS(\getApplication()->getConfig('Application')?->uri, $node->getNodeNamespace().":".$node->getKey());
		    else
        		$element = $this->getDocument()->createElement($node->getKey());
			
			if ($node instanceof Attributes)
			{			    
				$attrList = $node->getAttributes();
				if (!empty($attrList))
					foreach ($attrList as $entityKey=>$entityValue)
					    if ($entityValue !== NULL)
					       $element->setAttribute($entityKey, $entityValue);
			}
			
			if ($node instanceof Namespaces\Attribute)
			{
				$attribute = $this->getDocument()->createAttributeNS(\getApplication()->getConfig('Application')?->uri, $node->getAttributeNamespace());
				$element->appendChild($attribute);				
			}
			
			if ($node instanceof Nodes)
			{					
				$nodeList = $node->getNodes();
				
				if (!empty($nodeList))
					foreach ($nodeList as $entityKey=>$entityValue)
					{
					    if ($entityValue instanceof Exportable)
							$this->processNode($entityKey, $entityValue, $element);                      						
						else
							$element->appendChild($this->importNode($entityKey, $entityValue));
					}
			}
			
			if ($node instanceof Text)
			{
				$textList = $node->getText();
				
				if (!empty($textList))
				foreach ($textList as $entityKey=>$entityValue)
					$element->appendChild($this->importNode($entityKey, $entityValue));
			} 
			
			if ($node instanceof Fragment) 
			{
				foreach ($node->getFragment() as $name => $content)
				{
					
					if (!$content)
						continue;
					
					$newElement = $element->ownerDocument->createElement($name);
					$fragment = $element->ownerDocument->createDocumentFragment();

					$fragment->appendXML($content);
					$newElement->appendChild($fragment);

					$element->appendChild($newElement);
				}
			}
				
			$rootElement->appendChild($element);					
		}
		elseif($node instanceof \DOMNode) 
		{			
			if ($node instanceof \DOMDocument)
			{
				$root = $this->getDocument()->createElement($key); 
				$element = $this->getDocument()->importNode($node->documentElement, TRUE);
				$root->appendChild($element);
				$rootElement->appendChild($root);
				return;
			}
			
			$rootElement->appendChild($node);
		}
		elseif (is_array($node))
		{
			$element = $rootElement;
			if (is_string($key))
			{
				$element = $this->document->createElement($key);
				$rootElement->appendChild($element);
			}
				
			foreach ($node as $nodeKey=>$value)
			{
				if (is_numeric($nodeKey))
					$nodeKey =  NULL;
					 
				$this->processNode($nodeKey, $value, $element);				
			}
				 
		}		
		elseif (is_int($node) || is_bool($node) || is_double($node) || is_float($node) || is_string($node)) 
		{
			if (!$key)
			    throw new Error("Node key is NULL.");
			
			$rootElement->setAttribute($key, $node);
		}
		else
		{
			$element = $this->document->createElement($key, $node);
			$rootElement->appendChild($element);
		}	
	}
}