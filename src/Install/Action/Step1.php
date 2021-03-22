<?php
namespace Install\Action;
use Application\Entity;
use Core\Event;
use Module\Uploader\Exception\Error;
use Core\Database\MySql\Query;

/**
 * Class Step1
 * Installation sep one, created database tables and some data
 * @package Install\Action
 */
class Step1 extends Event\Action
{
	public function __invoke()
	{	
		$application 	= \getApplication();
		$application->setStylesheet('install.xsl');

		$module     = $this->getOwner();
		$cfg        = $application->getConfig();
        $db         = $cfg->getDatabase($cfg->getApplication()->database);

        $query = new Query('DROP TABLE IF EXISTS `content`;');
        $query();

        $query = new Query('drop table if exists `account`;');
        $query();

        $query = new Query('            
            CREATE TABLE `account` 
            (
                `accountID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(45) NOT NULL,
                `password` BINARY(32) NULL,
            PRIMARY KEY (`accountID`),
            UNIQUE INDEX `idx_accountID` (`accountID` ASC),
            UNIQUE INDEX `idx_account_email` (`email` ASC)
            ) ENGINE=InnoDB
        ');
        $query();

        $query = new Query("INSERT INTO `account` VALUES (1,'demouser','xxx');");
        $query();


        $query = new Query('CREATE TABLE `content` (
              `contentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `title` varchar(45) DEFAULT NULL,
              `description` varchar(45) DEFAULT NULL,
              `text` text DEFAULT NULL,
              `created` datetime DEFAULT NULL,
              `published` datetime DEFAULT NULL,
              `modified` datetime DEFAULT NULL,
              `authorID` int(10) unsigned NOT NULL,
              `parentID` int(10) unsigned DEFAULT NULL,
              `tags` varchar(45) DEFAULT NULL,
              PRIMARY KEY (`contentID`),
              KEY `fk_content_author_idx` (`authorID`),
              KEY `fk_content_parent_idx` (`parentID`),
              CONSTRAINT `fk_content_author` FOREIGN KEY (`authorID`) REFERENCES `account` (`accountID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_content_parent` FOREIGN KEY (`parentID`) REFERENCES `content` (`contentID`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB');
        $query();

        $query = new Query("INSERT INTO `content` VALUES (1,'Welcome to LazyFramework','Demo page description','This is demo entry. You may put some text here.','2021-03-22 08:47:35',NULL,NULL,1,NULL,'LazyFramework'),(2,'New page','This is new one page','This is new page text. This page is so exiting, omg...','2021-03-22 10:06:53',NULL,NULL,1,NULL,NULL);");
        $query();

        $this->getOwner()->setAttribute('redirect', \getApplication(\Install::CONFIG)->getConfig()->getModule('install')->successURL);
	}
}