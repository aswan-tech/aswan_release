<?php
/**
 * Magento Setup Script
 *
 * Script to add new column to the orders table and create new statuses.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Tuesday, June 12, 2012
 * @copyright	Four cross media
 */

$installer = $this;
 
$installer->startSetup();
 
$installer->run("
	ALTER TABLE {$this->getTable('sales_flat_order')} 
	ADD `sent_to_erp` SMALLINT( 1 ) NOT NULL DEFAULT '0';
	
	INSERT INTO {$this->getTable('sales_order_status')} (status, label) values ('created', 'Created'), ('COD_Verification_Pending', 'COD Verification Pending'), ('COD_Verification_Successful', 'COD Verification Successful'), ('COD_Verification_Unsucessful', 'COD Verification Unsucessful'),('confirmed_by_warehouse', 'Confirmed By Warehouse'),('shipped', 'Shipped'),('delivered', 'Delivered'),('not_delivered', 'Not Delivered'),('order_unsuccessful', 'Order Unsuccessful');
	
	INSERT INTO {$this->getTable('sales_order_status_state')} (status, state, is_default) values ('created', 'pending_payment', 0), ('created', 'processing', 0), ('COD_Verification_Pending', 'new', 0), ('COD_Verification_Successful', 'new', 0), ('COD_Verification_Unsucessful', 'new', 0), ('confirmed_by_warehouse', 'new', 0), ('confirmed_by_warehouse', 'processing', 0), ('shipped', 'processing', 0), ('shipped', 'complete', 0), ('delivered', 'complete', 0), ('not_delivered', 'processing', 0), ('not_delivered', 'complete', 0), ('order_unsuccessful', 'closed', 0);

	INSERT INTO {$this->getTable('sales_order_status_label')} (status, store_id, label) values ('created', 1, 'Created'), ('COD_Verification_Pending', 1, 'COD Verification Pending'), ('COD_Verification_Successful', 1, 'COD Verification Successful'), ('COD_Verification_Unsucessful', 1, 'COD Verification Unsucessful'), ('confirmed_by_warehouse', 1, 'Confirmed By Warehouse'), ('shipped', 1, 'Shipped'), ('delivered', 1, 'Delivered'), ('not_delivered', 1, 'Not Delivered'), ('order_unsuccessful', 1, 'Order Unsuccessful');
	
	UPDATE {$this->getTable('sales_order_status')} set label='Cancelled' where status='canceled';
");
 
$installer->endSetup();