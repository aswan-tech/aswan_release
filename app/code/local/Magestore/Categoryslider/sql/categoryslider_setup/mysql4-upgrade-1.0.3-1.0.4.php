<?php

$installer = $this;
$installer->startSetup();

$installer->run("
	ALTER TABLE `categoryslider` ADD `mapimg1` text NOT NULL AFTER `weblink`;
");

$installer->endSetup();