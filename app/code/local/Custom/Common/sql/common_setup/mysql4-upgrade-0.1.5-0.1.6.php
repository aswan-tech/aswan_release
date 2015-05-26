<?php
$this->startSetup()->run("
	
	ALTER TABLE `categories_product_salable` ADD `cat_url` varchar( 255 ) DEFAULT NULL;
	
	ALTER TABLE `categories_product_salable` ADD `cat_active` SMALLINT( 3 ) NOT NULL COMMENT '''0'' => No, ''1''=>''Yes''';
	
")->endSetup();
