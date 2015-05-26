<?php

class FCM_Zipcodeimport_Block_Adminhtml_Zipcodeimport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('zipcodeimport_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('zipcodeimport')->__('Zip Code Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('zipcodeimport')->__('Zip Code Information'),
          'title'     => Mage::helper('zipcodeimport')->__('Zip Code Information'),
          'content'   => $this->getLayout()->createBlock('zipcodeimport/adminhtml_zipcodeimport_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}