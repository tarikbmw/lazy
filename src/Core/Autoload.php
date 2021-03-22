<?php
namespace Core;

/**
 * Autoloader class
 * @author tarik
 */
class Autoload
{
    /**
     * Array of registered classnames
     * @var array
     */
    public static array $loadedClasses = [];
    
    const PATH = "../src/";
    const EXT = ".php";
    
	function __construct()
	{
			spl_autoload_extensions(self::EXT);
			spl_autoload_register(function(string $className)
            {
                $file = self::PATH.str_ireplace("\\", "/", $className).self::EXT;
                if (!file_exists($file))
                    throw new \Exception("Could not find class `$className` in file `$file`");
                require_once($file);
                Autoload::$loadedClasses[$className] = $file;
            });
	}

    /**
     * Show loaded classes
     * @return string
     */
	public static function dump():string
	{
	    return '<!--
Total count: '. count(self::$loadedClasses).'
'.print_r(self::$loadedClasses, true).'-->';
	}
}