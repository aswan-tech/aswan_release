<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('fcm_zipcodeimport')} ADD `express` int(2) NOT NULL;
ALTER TABLE {$this->getTable('fcm_zipcodeimport')} ADD `standard` int(2) NOT NULL;


    ");

$installer->endSetup(); 