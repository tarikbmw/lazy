<?php
namespace Core\Module;

/**
 * Module descriptor for preloader
 * @package Core\Module
 */
class Descriptor
{
    /**
     * Descriptor constructor.
     * @param string    $className      class name
     * @param array     $router         router to events
     */
    public function __construct(private string $className, private array $router)
    {
    }

    /**
     * Get descriptor class name
     * @return string
     */
    public function getClassName():string
    {
        return  $this->className;
    }

    /**
     * Get events router
     * @return array
     */
    public function getRouter():array
    {
        return $this->router;
    }
}
