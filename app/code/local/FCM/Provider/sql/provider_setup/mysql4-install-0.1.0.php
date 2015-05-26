<?php

$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('provider')};
CREATE TABLE {$this->getTable('provider')} (
`provider_id` int(11) unsigned NOT NULL auto_increment,
`blinkecarrier_id` varchar(50) NOT NULL,
`shippingprovider_name` varchar(100) NOT NULL,
`shippingprovider_hovertext` varchar(255) NOT NULL,
`shippingprovider_action` varchar(255) NOT NULL,
  PRIMARY KEY (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();