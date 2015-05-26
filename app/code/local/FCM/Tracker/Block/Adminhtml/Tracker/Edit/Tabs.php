<?php

class FCM_Tracker_Block_Adminhtml_Tracker_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('tracker_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('tracker')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('tracker')->__('Item Information'),
          'title'     => Mage::helper('tracker')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('tracker/adminhtml_tracker_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}