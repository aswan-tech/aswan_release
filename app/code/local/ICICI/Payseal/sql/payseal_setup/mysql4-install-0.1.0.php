<?php



$installer = $this;
/* @var $installer ICICI_payseal_Model_Mysql4_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE `{$this->getTable('payseal_api_debug')}` (
  `debug_id` int(10) unsigned NOT NULL auto_increment,
  `transact_id` varchar(50) NOT NULL,
  `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `request_body` text,
  `response_body` text,
  PRIMARY KEY  (`debug_id`),
  KEY `debug_at` (`debug_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();