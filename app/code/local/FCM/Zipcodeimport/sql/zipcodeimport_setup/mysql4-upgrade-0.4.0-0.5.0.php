<?php

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('fcm_shippingcarriers')} (
  `blinkecarrier_id` varchar(50) NOT NULL ,
  `carrier_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('fcm_zipcodeimport')} 
	ADD `blinkecarrier_id` VARCHAR( 50 ) NOT NULL DEFAULT '';

ALTER TABLE {$this->getTable('sales_flat_quote')} 
	ADD `blinkecarrier_id` VARCHAR( 50 ) NOT NULL DEFAULT '';
	
ALTER TABLE {$this->getTable('sales_flat_order')} 
	ADD `blinkecarrier_id` VARCHAR( 50 ) NOT NULL DEFAULT '';

");

$installer->endSetup(); 