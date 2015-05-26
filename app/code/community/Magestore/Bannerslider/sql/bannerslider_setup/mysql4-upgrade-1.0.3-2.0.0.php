<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('bannerslider')} ADD `stores` text default '' AFTER `title`;

ALTER TABLE {$this->getTable('bannerslider')} ADD `is_home` tinyint(1) NOT NULL default '0' AFTER `update_time`;
ALTER TABLE {$this->getTable('bannerslider')} ADD `categories` text default '' AFTER `is_home`;

    ");

$installer->endSetup();