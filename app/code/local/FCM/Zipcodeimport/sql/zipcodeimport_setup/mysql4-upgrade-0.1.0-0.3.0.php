<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('fcm_zipcodeimport')} ADD `appointment` int(2) NOT NULL;
ALTER TABLE {$this->getTable('fcm_zipcodeimport')} ADD `overnite` int(2) NOT NULL;
ALTER TABLE {$this->getTable('fcm_zipcodeimport')} ADD `cod` int(2) NOT NULL;

    ");

$installer->endSetup();