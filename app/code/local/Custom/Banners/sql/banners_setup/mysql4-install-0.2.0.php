<?php
$installer=$this;
$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('banners')};
CREATE TABLE IF NOT EXISTS {$this->getTable('banners')} (
  `banner_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `banner_title` varchar(100) NOT NULL DEFAULT '',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `banner_path` varchar(100) NOT NULL DEFAULT '',
  `banner_url` varchar(255) NOT NULL DEFAULT '',
  `banner_type` tinyint(4) NOT NULL,
  `banner_status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`banner_id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
$installer->endSetup();
?>

