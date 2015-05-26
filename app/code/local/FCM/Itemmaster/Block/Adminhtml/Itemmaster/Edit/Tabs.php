<?php

class FCM_Itemmaster_Block_Adminhtml_Itemmaster_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('itemmaster_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('itemmaster')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('itemmaster')->__('Item Information'),
          'title'     => Mage::helper('itemmaster')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('itemmaster/adminhtml_itemmaster_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}