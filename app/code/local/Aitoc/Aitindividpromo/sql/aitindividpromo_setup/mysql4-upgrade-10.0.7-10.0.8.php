<?php

$installer = $this;
$installer->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('aitoc_salesrule_assign_cutomer')} ADD `coupon_code` VARCHAR( 254 ) NULL 
");

$installer->endSetup();