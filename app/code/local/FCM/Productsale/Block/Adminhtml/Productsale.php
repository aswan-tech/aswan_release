<?php
class FCM_Productsale_Block_Adminhtml_Productsale extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_productsale';
    $this->_blockGroup = 'productsale';
    $this->_headerText = Mage::helper('productsale')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('productsale')->__('Add Item');
    parent::__construct();
  }
}