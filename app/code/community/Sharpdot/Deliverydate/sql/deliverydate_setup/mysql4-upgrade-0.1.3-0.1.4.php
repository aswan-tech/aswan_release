<?php
$installer = $this;
$installer->startSetup();


//Add time slot to order
$eav = new Mage_Eav_Model_Entity_Setup('sales_setup');
$eav->addAttribute('order', 'shipping_time_slot', array(
	'type'		=> 'varchar',
	'input'		=> 'text',
	'required'	=> 0,
	'label'		=> 'Shipping Time Slot'
));

$installer->endSetup();