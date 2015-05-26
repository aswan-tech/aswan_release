<?php
class Magestore_Categoryslider_Block_Adminhtml_Categoryslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_categoryslider';
    $this->_blockGroup = 'categoryslider';
    $this->_headerText = Mage::helper('categoryslider')->__('Manage Department Banners');
    $this->_addButtonLabel = Mage::helper('categoryslider')->__('Add Banner');
    parent::__construct();
  }
}