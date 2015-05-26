<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('saledepartments')};
CREATE TABLE {$this->getTable('saledepartments')} (
  `saledepartment_id` int(11) unsigned NOT NULL auto_increment,
  `department_id` int(11) NOT NULL,
  `department_url` varchar(255) NOT NULL,
  PRIMARY KEY (`saledepartment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();