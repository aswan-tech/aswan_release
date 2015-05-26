<?php

$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('fcm_payment')};
CREATE TABLE {$this->getTable('fcm_payment')} (
`payment_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`payment_method_type` VARCHAR( 100 ) NOT NULL ,
`payment_method_name` VARCHAR( 255 ) NOT NULL ,
`payment_method_code` VARCHAR( 100 ) NOT NULL ,
PRIMARY KEY ( `payment_id` )
) ENGINE = InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();