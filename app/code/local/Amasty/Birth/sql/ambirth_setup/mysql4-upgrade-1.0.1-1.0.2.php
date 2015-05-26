<?php
/**
* @copyright Amasty.
*/  
$this->startSetup();

$this->run("

ALTER TABLE `{$this->getTable('ambirth/log')}` ADD `type` ENUM( 'birth', 'reg' ) DEFAULT 'birth' NOT NULL AFTER `y` ;

");

$this->endSetup(); 