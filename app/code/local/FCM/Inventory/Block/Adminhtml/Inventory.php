<?php
class FCM_Inventory_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_inventory';
    $this->_blockGroup = 'inventory';
    $this->_headerText = Mage::helper('inventory')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('inventory')->__('Add Item');
    parent::__construct();
  }
}