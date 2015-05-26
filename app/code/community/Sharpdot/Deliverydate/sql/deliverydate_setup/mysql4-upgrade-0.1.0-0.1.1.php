<?php
$installer = $this;
$installer->startSetup();

//Add Column to quote table
$this->_conn->addColumn($this->getTable('sales_flat_order'), 'shipping_arrival_date', 'datetime');

$installer->endSetup();