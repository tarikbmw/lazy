<?php
use Core\Pattern\Traits\Singleton;
use Core\Pattern\Traits\Stylesheet;

/**
 * Install class
 * Demo installation application
 * @author tarik
 */
final class Install extends \Core\Framework
{
    use Stylesheet, Singleton;

    /**
     * Install configuration
     */
    const CONFIG = '../cfg/Install.xml';

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct(self::CONFIG);

        // Set node key
        $this->setKey($this->getConfig()->getApplication()->name);

        // Setup session
        $this->getSession($_GET[$this->getConfig()->getSession()->name] ??
            $_COOKIE[$this->getConfig()->getSession()->name]  ?? NULL);
    }

    /**
     * After render process
     * {@inheritDoc}
     * @see \Core\Application::process()
     */
    protected function process():void
    {
        // Add current stylesheet to render
        if ($this->getStylesheet())
            $this->getRender()->setStylesheetURL($this->getStylesheet());

        parent::process();
    }

    /**
     * Returning output buffer
     * @return string
     */
    public function __toString():string
    {
        return $this->getRender();
    }
}