<?php

class FCM_Logger_Block_Adminhtml_Logger_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('logger_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('logger')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('logger')->__('Item Information'),
          'title'     => Mage::helper('logger')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('logger/adminhtml_logger_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}