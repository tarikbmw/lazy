<?php
namespace Install;
use Core\Autoload;
use Core\Exception\Error;
use Core\Log;
use Core\Render\Stylesheet\XSL;
use Core\Setup;

error_reporting(\E_ALL);
ini_set('display_errors', 'On');

require_once "../src/Core/Common.php";
new Autoload();

/**
 * Path to configuration
 */
const CONFIG = '../cfg/Install.xml';

$application = NULL;

try
{
    $application = \getApplication(CONFIG);

    $charSet = $application->getConfig()->getApplication()->charset;
    $mimeType = $application->getConfig()->getOutput($application->getOutputType())->mime;

    header("Content-type: $mimeType; charset=$charSet");
    $application();
    echo $application;
}
catch (Error $e)
{
    if ($application instanceof \Core\Framework)
    {
        $render = $application->getRender();
        $render->setStylesheet(new XSL("install.xsl"));
        $render->setNode("exception", ["title"=>"Error", "message" => $e->getMessage(), "response"=>"rejected"]);
        $render->process();
        echo $render;

        return;
    }

    throw $e;
}
catch (\Throwable $e)
{
    (new Log)->Write($e->getTraceAsString(), $e->getMessage(), Log::ERROR);

    if ($application instanceof \Core\Framework)
    {
        $render = $application->getRender();
        $render->setStylesheet(new XSL("install.xsl"));
        $render->setNode("exception", ["title"=>"Fatal Error", "message" => $e->getMessage(), "response"=>"rejected", "debug"=>$e->getTraceAsString()]);
        $render->process();
        echo $render;
        return;
    }

    Setup::getInstance(CONFIG);
    header('Content-type: text/xml; charset=utf-8');
    $xml = new \Core\Render\XML();
    $xml->getDocument();
    $xml->setStylesheet(new XSL("install.xsl"));
    $xml->exception = ["title"=>"Fatal Error", "response"=>"rejected", "message"=>$e->getMessage()];

    echo $xml;
}
