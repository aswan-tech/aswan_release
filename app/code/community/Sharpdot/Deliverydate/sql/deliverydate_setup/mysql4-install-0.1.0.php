<?php
$installer = $this;
$installer->startSetup();

//Add Column to address table
//$this->_conn->addColumn($this->getTable('sales_flat_quote_address'), 'shipping_arrival_date', 'varchar(100)');

//Add arrival date to order
$eav = new Mage_Eav_Model_Entity_Setup('sales_setup');
$eav->addAttribute('order', 'shipping_arrival_date', array(
	'type'		=> 'datetime',
	'input'		=> 'date',
	'backend'	=> 'eav/entity_attribute_backend_datetime',
	'required'	=> 0,
	'label'		=> 'Desired Arrival Date'
));

$installer->endSetup();