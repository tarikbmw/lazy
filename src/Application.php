<?php
use Core\Feature\Access;
use Core\Pattern\Traits\Singleton;
use Core\Pattern\Traits\Stylesheet;

/**
 * Custom application class
 * @author tarik
 */
final class Application extends \Core\Framework
{
    use Stylesheet, Singleton;

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();

        // Set node key
        $this->setKey($this->getConfig()->getApplication()->name);

        // Setup session
        $this->getSession($_GET[$this->getConfig()->getSession()->name] ??
            $_COOKIE[$this->getConfig()->getSession()->name]  ?? NULL);

        // Register access conditions used by event listener
        $this->registerCondition
        (
            fn($instance) => $instance instanceof Access && !$instance->accessible(),
            fn($instance) => $instance->rejection()
        );
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