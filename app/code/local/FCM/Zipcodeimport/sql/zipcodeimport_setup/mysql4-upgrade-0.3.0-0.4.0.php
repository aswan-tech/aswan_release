<?php

$installer = $this;

$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('featured_products')};
	CREATE TABLE {$this->getTable('featured_products')} 
	(
	`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`entity_id` INT( 10 ) NOT NULL ,
	`product_id` varchar(255) NOT NULL
	) ENGINE = MYISAM ;
	");

$installer->endSetup();