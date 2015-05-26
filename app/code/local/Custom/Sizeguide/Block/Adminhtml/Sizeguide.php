<?php
class Custom_Sizeguide_Block_Adminhtml_Sizeguide extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_sizeguide';
    $this->_blockGroup = 'sizeguide';
    $this->_headerText = Mage::helper('sizeguide')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('sizeguide')->__('Add Item');
    parent::__construct();
  }
}