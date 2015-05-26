<?php

class FCM_Premiumalert_Block_Adminhtml_Premiumalert_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('premiumalert_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('premiumalert')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('premiumalert')->__('Item Information'),
          'title'     => Mage::helper('premiumalert')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('premiumalert/adminhtml_premiumalert_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}