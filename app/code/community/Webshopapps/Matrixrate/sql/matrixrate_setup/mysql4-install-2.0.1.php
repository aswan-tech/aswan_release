<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('shipping_matrixrate')};
DROP TABLE IF EXISTS {$this->getTable('shipping_zones')};

CREATE TABLE {$this->getTable('shipping_matrixrate')} (
`pk` int(10) unsigned NOT NULL AUTO_INCREMENT,
`website_id` int(11) NOT NULL DEFAULT '0',
`zone` varchar(255) NOT NULL,
`condition_name` varchar(20) NOT NULL DEFAULT '',
`condition_from_value` decimal(12,4) NOT NULL DEFAULT '0.0000',
`condition_to_value` decimal(12,4) NOT NULL DEFAULT '0.0000',
`shipping_charge` decimal(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'shipping charge in percentage of order amount',
PRIMARY KEY (`pk`),
UNIQUE KEY `zone` (`zone`,`website_id`,`condition_name`,`condition_from_value`,`condition_to_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('shipping_zones')} (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`country_code` VARCHAR( 255 ) NOT NULL ,
`zone` VARCHAR( 255 ) NOT NULL ,
`delivery_type` VARCHAR( 255 ) NOT NULL ,
`shipping_provider` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `id` ),
UNIQUE KEY `shipzone` (`country_code`,`zone`,`delivery_type`,`shipping_provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
