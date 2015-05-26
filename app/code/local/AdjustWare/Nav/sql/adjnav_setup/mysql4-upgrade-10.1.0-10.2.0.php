<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run('

CREATE TABLE IF NOT EXISTS `'.$this->getTable('adjnav_eav_attribute_option_stat').'` (
  `option_id` INT UNSIGNED NOT NULL,
  `attribute_id` SMALLINT UNSIGNED NOT NULL DEFAULT "0",
  `uses` DECIMAL(12,4) UNSIGNED NOT NULL,
  PRIMARY KEY (`option_id`),
  KEY `adjnav_option_uses` (`uses`),
  CONSTRAINT `adjnav_eav_attr_opt_stat_to_eav_opt` FOREIGN KEY `adjnav_eav_attr_opt_stat_to_eav_opt` (`option_id`) 
  REFERENCES `'.$this->getTable('eav_attribute_option').'` (`option_id`) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `'.$this->getTable('adjnav_eav_attribute_option_hit').'` (
  `hit_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
  `option_id` INT UNSIGNED NOT NULL, 
  PRIMARY KEY (`hit_id`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `'.$this->getTable('adjnav_eav_attribute_stat').'` (
  `attribute_id` SMALLINT UNSIGNED NOT NULL, 
  `uses` DECIMAL(12,4) UNSIGNED NOT NULL, 
  PRIMARY KEY (`attribute_id`),
  CONSTRAINT `adjnav_eav_attr_stat_to_eav_attr` FOREIGN KEY `adjnav_eav_attr_stat_to_eav_attr` (`attribute_id`) REFERENCES `'.$this->getTable('eav_attribute').'` (`attribute_id`) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `'.$this->getTable('adjnav_cron').'` (
  `code` varchar(50) NOT NULL,
  `last_run` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB;

INSERT IGNORE INTO `'.$this->getTable('adjnav_cron').'` (`code`) VALUES ("collect_attribute_stats");

');

$installer->run('

DROP TABLE IF EXISTS `'.$this->getTable('adjnav_catalog_product_index_configurable').'`;

CREATE TABLE `'.$this->getTable('adjnav_catalog_product_index_configurable').'` LIKE `'.$this->getTable(Mage::helper('adjnav/version')->getBaseIndexTable()).'`;

');


$installer->endSetup();

