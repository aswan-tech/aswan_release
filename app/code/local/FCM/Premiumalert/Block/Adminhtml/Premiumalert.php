<?php
class FCM_Premiumalert_Block_Adminhtml_Premiumalert extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_premiumalert';
    $this->_blockGroup = 'premiumalert';
    $this->_headerText = Mage::helper('premiumalert')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('premiumalert')->__('Add Item');
    parent::__construct();
  }
}