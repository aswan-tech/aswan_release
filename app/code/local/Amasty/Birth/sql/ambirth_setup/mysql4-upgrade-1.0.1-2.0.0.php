<?php
/**
* @copyright Amasty.
*/  
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('ambirth/log')}` CHANGE `type` `type` VARCHAR( 32 ) NOT NULL; 
");

$this->endSetup(); 