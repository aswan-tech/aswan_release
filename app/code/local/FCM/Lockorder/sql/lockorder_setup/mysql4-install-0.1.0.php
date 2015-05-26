<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('lockorder')};
CREATE TABLE {$this->getTable('lockorder')} (
  `lockorder_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `lock_acquired` datetime NOT NULL,
  `lock_released` datetime NOT NULL,
  PRIMARY KEY (`lockorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
