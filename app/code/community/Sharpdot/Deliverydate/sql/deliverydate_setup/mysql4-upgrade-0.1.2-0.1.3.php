<?php
$installer = $this;
$installer->startSetup();

//Add Column to quote table
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_time_slot', 'varchar(255)');

$installer->endSetup();