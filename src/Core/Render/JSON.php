<?php
namespace Core\Render;
use Core\Exception\Error;
use Core\Feature\Exportable;
use Core\Feature\Export\{Attributes, Nodes, Text, Fragment};
use Core\Render\Stylesheet;

/**
 * Class JSON
 * JSON Render, creates JSON encoded string from array of Entities or Exportable objects
 * @package Core\Render
 */
class JSON extends \Core\Render
{
	/**
	 * Main document, element acceptor
	 * @var array
	 */
	private array $document = [];

	/**
	 * Output buffer
	 * @var string|null
	 */
	private ?string $json = '';

    /**
     * Render elements to json string
     * @return string|null
     * @throws Error
     */
	public function process():?string
	{
            if (!empty($this->nodes))
                foreach ($this->nodes as $item)
                    if ($item instanceof Exportable)
                        $this->processNode($item->getKey(), $item, $this->document);
                    else
                        return $this->json = json_encode($this->nodes) or NULL;

            return $this->json = json_encode($this->document) or NULL;
	}

	public function __toString():string
	{		
            return $this->json;
	}

    /**
	 * Parse nodes to XML document
	 * @param string 		$key			- current node Key
	 * @param mixed 		$node			- current node
	 * @param $rootElement	-  root element
	 */
	protected function processNode(?string $key, mixed $node, &$rootElement):void
	{   
		if ($node instanceof Exportable)
		{
		    $element = [];

			if ($node instanceof Attributes)
			    if (!empty($node->getAttributes()))
		            $element['attributes'] = $node->getAttributes();

			if ($node instanceof Nodes)
			{
				$nodeList = $node->getNodes();
				if (!empty($nodeList))
					foreach ($nodeList as $entityKey=>$entityValue)
					{
					    if ($entityValue instanceof Exportable)
                        {
                            $this->processNode($entityKey, $entityValue, $element);
                            continue;
                        }

                        if (isset($element[$entityKey]))
                            $element[][$entityKey] = $entityValue;
                        else
                            $element[$entityKey] = $entityValue;
					}
			}
			
			if ($node instanceof Text)
			{
				$textList = $node->getText();
				
				if (!empty($textList))
				foreach ($textList as $entityKey=>$entityValue)
                    $element['text'][$entityKey] = $entityValue;
			} 
			
			if ($node instanceof Fragment) 
			{
				foreach ($node->getFragment() as $name => $content)
				{
					if (!$content)
						continue;

                    $element['fragment'][$name] = $content;
				}
			}

			if (isset($rootElement[$node->getKey()]))
                $rootElement[][$node->getKey()] = new \ArrayObject($element);
			else
                $rootElement[$node->getKey()] = new \ArrayObject($element);
		}
		elseif (is_array($node))
		{
		    $element = [];

			if (is_string($key))
				$element[$key] = $node;

			foreach ($node as $nodeKey=>$value)
			{
				if (is_numeric($nodeKey))
					$nodeKey =  NULL;
					 
				$this->processNode($nodeKey, $value, $element[$key]);
			}

			$rootElement = $element;
		}		
		elseif (is_int($node) || is_bool($node) || is_double($node) || is_float($node) || is_string($node)) 
		{
			if (!$key)
			    throw new Error("Node key is NULL.");
			
			$element[$key] = $node;
			$rootElement = $element;
		}
		else
		{
			$element[][$key] = $node;
			$rootElement = $element;
		}
	}

    /**
     * Not used
     * @param Stylesheet $style
     */
    public function setStylesheet(Stylesheet $style):void
    {
    }

    /**
     * Not used
     * @param string $style
     */
    public function setStylesheetURL(string $style):void
    {
    }
}
