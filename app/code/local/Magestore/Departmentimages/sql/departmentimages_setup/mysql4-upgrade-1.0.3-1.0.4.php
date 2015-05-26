<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('departmentimages')} ADD `filename_hover` VARCHAR( 255 ) NOT NULL default '' AFTER `filename`;

    ");

$installer->endSetup(); 