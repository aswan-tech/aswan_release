<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `fcm_zipcodeimport` ADD `city` VARCHAR( 255 ) NOT NULL AFTER `state`;

");

$installer->endSetup(); 