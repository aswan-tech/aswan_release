<?php
$this->startSetup()->run("
	
	ALTER TABLE `sales_flat_order_item` ADD `pck_option` SMALLINT( 5 ) NOT NULL COMMENT '''0'' => No, ''1''=>''Yes''',ADD `pck_sku` VARCHAR( 255 ) NULL DEFAULT NULL ,ADD `pck_qty` DECIMAL( 12, 2 ) NULL DEFAULT NULL;
	
	ALTER TABLE `sales_flat_quote_item` ADD `pck_option` SMALLINT( 5 ) NOT NULL COMMENT '''0'' => No, ''1''=>''Yes''',ADD `pck_sku` VARCHAR( 255 ) NULL DEFAULT NULL ,ADD `pck_qty` DECIMAL( 12, 2 ) NULL DEFAULT NULL;
	
")->endSetup();
