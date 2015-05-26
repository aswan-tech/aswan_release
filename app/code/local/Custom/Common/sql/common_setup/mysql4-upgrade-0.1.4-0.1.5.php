<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('categories_product_salable')}(
  `increment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
  `category_id` INT UNSIGNED NOT NULL,
  `have_products_to_display` TINYINT(3) NULL,
  PRIMARY KEY (`increment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$installer->endSetup();