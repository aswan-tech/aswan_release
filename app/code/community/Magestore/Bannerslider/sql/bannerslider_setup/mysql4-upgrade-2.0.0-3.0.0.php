<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('bannerslider')} ADD `preview` varchar(255) default '' AFTER `is_home`;

    ");

$installer->endSetup();