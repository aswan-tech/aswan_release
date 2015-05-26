<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `fcm_zipcodeimport` ADD INDEX ( `zip_code` );

");

$installer->endSetup(); 