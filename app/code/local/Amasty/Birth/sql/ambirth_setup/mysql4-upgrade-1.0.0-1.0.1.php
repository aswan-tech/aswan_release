<?php
/**
* @copyright Amasty.
*/  
$this->startSetup();

$this->run("
CREATE TABLE `{$this->getTable('ambirth/log')}` (
  `log_id` mediumint(8) unsigned NOT NULL auto_increment,
  `customer_id` mediumint(8) unsigned NOT NULL,
  `y` year unsigned NOT NULL,
  `sent_date` datetime NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$this->endSetup(); 