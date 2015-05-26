<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('aitoc_salesrule_assign_cutomer')};
CREATE TABLE {$this->getTable('aitoc_salesrule_assign_cutomer')} (
  `entity_id` int(10) NOT NULL default '0',
  `customer_id` int(10) NOT NULL default '0',
  UNIQUE KEY `aitoc_entity_customer` (`entity_id`,`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
