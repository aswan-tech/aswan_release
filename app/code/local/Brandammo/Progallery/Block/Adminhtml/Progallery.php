<?php
class Brandammo_Progallery_Block_Adminhtml_Progallery extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_progallery';
    $this->_blockGroup = 'progallery';
    $this->_headerText = Mage::helper('progallery')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('progallery')->__('Add Item');
    parent::__construct();
  }
}