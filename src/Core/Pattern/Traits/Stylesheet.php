<?php
namespace Core\Pattern\Traits;

/**
 * Adding custom stylesheets to object
 * @author tarik
 */
trait Stylesheet 
{
    /**
     * @var string
     */
    protected ?string $stylesheet = null;
    
    /**
     * Set stylesheet url
     * @param string $url
     */
    public function setStylesheet(string $url):void
    {
        $this->stylesheet = $url;
    }

    /**
     * Get stylesheet
     * @return string|NULL
     */
    public function getStylesheet():?string
    {
        return $this->stylesheet;
    }
}

