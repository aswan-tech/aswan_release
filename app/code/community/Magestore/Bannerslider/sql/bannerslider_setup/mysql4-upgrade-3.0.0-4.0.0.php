<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('bannerslider')} ADD `sort_id` int(11) AFTER `is_home`;

    ");

$installer->endSetup();	