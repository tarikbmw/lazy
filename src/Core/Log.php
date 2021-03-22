<?php
namespace Core;

/**
 * Logger
 */
class Log
{
    /**
     * Log Types
     * @var integer
     */
    const LOG 		= 1;
    const ERROR 	= 2;
    const WARNING 	= 3;
    const DEBUG 	= 4;
    
    /**
     * Log print string template
     * @var string
     */
    const TPL = "%s %s(%s): %s \r\n";
    
    /**
     * Enable logging
     * @var boolean
     */
    protected bool $bEnabled;
    
    /**
     * Path to log file
     * @var string
     */
    protected string $path;
    
    /**
     * Log file name
     * @var string
     */
    protected string $name;
    
    /**
     * Logging string date format
     * @var string
     */
    protected string $dateFormat;

    /**
     * Add date in log filename
     * @var bool
     */
    protected bool $showDateInFilename;

    /**
     * Filename date format to add
     * @var string
     */
    protected string $filenameDateFormat;
    
    /**
     * Log type names
     * @var array
     */
    private array $types =
    [
        Log::LOG   => "LOG",
        Log::ERROR => "ERROR",
        Log::DEBUG => "DEBUG",
        Log::WARNING => "WARNING",
    ];

    /**
     * Log constructor.
     */
    public function __construct()
    {
        $config = Setup::getInstance()->getLog();
        
        $this->bEnabled           = $config->enabled;
        $this->showDateInFilename = $config->showDate;
        $this->filenameDateFormat = $config->filenameDateFormat;
        
        $this->path               = $config->path;
        $this->name               = $config->name;
        $this->dateFormat         = $config->dateFormat;
    }

    /**
     * Write message to log
     * @param string $sender message owner
     * @param string $message message text
     * @param int $type type (see $this->types)
     */
    public function write(string $sender, string $message, int $type = Log::LOG)
    {
        if (!$this->bEnabled)
            return;
            
        $date = new \DateTime();
        $fdate = $this->showDateInFilename ? $date->format($this->filenameDateFormat).'.' : '';
        $file = fopen(sprintf("%s/%s%s", $this->path, $fdate, $this->name), "a");
        if (!$file)
            return;

        fwrite($file, sprintf(self::TPL, $date->format($this->dateFormat), $this->types[$type], $sender, $message));
        fclose($file);
    }
}