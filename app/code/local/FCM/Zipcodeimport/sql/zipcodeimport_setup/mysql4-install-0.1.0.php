<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('fcm_zipcodeimport')};
CREATE TABLE {$this->getTable('fcm_zipcodeimport')} (
  `zipcodeimport_id` int(11) unsigned NOT NULL auto_increment,
  `zip_code` int(11) unsigned NOT NULL,
  `state` varchar(255) NULL default '',
  PRIMARY KEY (`zipcodeimport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 