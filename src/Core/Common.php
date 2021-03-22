<?php
/**
 * Common functions
 * @author tarik
 */
require_once('Autoload.php');

/**
 * Get framework or application instance
 * @param string|null $configuration path to an application settings XML file
 * @return \Core\Framework
 * @throws Exception
 */
function getApplication(?string $configuration = \Core\Setup::SETTINGS):\Core\Framework
{
    static $instance;
    if ($instance instanceof \Core\Framework)
        return $instance;

    if ($configuration && !file_exists($configuration))
        throw new \Exception('Configuration file missed. '.$configuration);

    $classname = Core\Setup::getInstance($configuration)->getApplication()->class ?? NULL;
    if (!$classname)
    	throw new \Exception('Framework class name missed in configuration.');

    if (!class_exists($classname))
        throw new \Exception("Could not find application class `$classname`.");
    
	return $instance = $classname::getInstance($configuration);
}