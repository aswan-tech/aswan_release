<?php

class FCM_Productsale_Block_Adminhtml_Productsale_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('productsale_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('productsale')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('productsale')->__('Item Information'),
          'title'     => Mage::helper('productsale')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('productsale/adminhtml_productsale_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}