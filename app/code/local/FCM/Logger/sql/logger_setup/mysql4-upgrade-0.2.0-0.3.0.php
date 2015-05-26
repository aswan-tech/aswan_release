<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('fcm_cron')} ADD `cron_key` varchar(255) NOT NULL;
INSERT INTO {$this->getTable('fcm_cron')} (`cron_key`, `cron_name`) VALUES
	('customer_export', 'Customer Export'),
	('order_fulfillment', 'Order Fulfillment'),
	('order_confirm', 'Order Confirmation'),
	('order_cancel', 'Order Cancel'),
	('order_shipment', 'Order Shipment'),
	('product_inventory', 'Product Inventory'),
	('item_master', 'Item Master'),
	('price_update', 'Price Update'),
	('image_update', 'Image Update');

    ");

$installer->endSetup(); 