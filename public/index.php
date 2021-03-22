<?php
namespace Application;
use Core\Autoload;
use Core\Exception\Error;
use Core\Log;
use Core\Render\Stylesheet\XSL;
use Core\Setup;

error_reporting(\E_ALL);
ini_set('display_errors', 'Off');

require_once "../src/Core/Common.php"; 
new Autoload();
$application = NULL;

try 
{
	$application = \getApplication();
	$cfg = $application->getConfig();

	$charSet = $cfg->getApplication()->charset;
	$mimeType = $cfg->getOutput($application->getOutputType())->mime;
	
	header("Content-type: $mimeType; charset=$charSet");	
	$application();
	echo $application;
}
catch (Error $e)
{
	if ($application instanceof \Core\Framework)
	{
		$render = $application->getRender();
		$render->setStylesheet(new XSL("main.xsl"));
		$render->setNode("exception", ["title"=>"Rejected", "message" => $e->getMessage(), "response"=>"rejected"]);
		$render->process();
		echo $render;		
		
		return;
	}
}
catch (\Throwable $e)
{			
	if ($application instanceof \Core\Framework)
	{
		$render = $application->getRender();
		$render->setStylesheet(new XSL("main.xsl"));
		$render->setNode("exception", ["title"=>"Error", "message" => $e->getMessage(), "response"=>"rejected", "debug"=>$e->getTraceAsString()]);
		$render->process();		
		echo $render;
		(new Log)->Write('Exception', $e->getMessage(), Log::ERROR);
		return;
	}

    Setup::getInstance();
	header('Content-type: text/xml; charset=utf-8');	
	$xml = new \Core\Render\XML();
		$xml->getDocument();
		$xml->setStylesheet(new XSL("main.xsl"));
		$xml->exception = ["title"=>"Fatal error", "response"=>"rejected", "message"=>$e->getMessage()];
		
	echo $xml;	
	
	(new Log)->Write($e->getTraceAsString(), $e->getMessage(), Log::ERROR);
}