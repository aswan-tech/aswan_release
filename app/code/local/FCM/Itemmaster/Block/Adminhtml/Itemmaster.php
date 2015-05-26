<?php
class FCM_Itemmaster_Block_Adminhtml_Itemmaster extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_itemmaster';
    $this->_blockGroup = 'itemmaster';
    $this->_headerText = Mage::helper('itemmaster')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('itemmaster')->__('Add Item');
    parent::__construct();
  }
}