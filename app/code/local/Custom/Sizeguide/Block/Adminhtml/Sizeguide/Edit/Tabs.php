<?php

class Custom_Sizeguide_Block_Adminhtml_Sizeguide_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('sizeguide_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('sizeguide')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('sizeguide')->__('Item Information'),
          'title'     => Mage::helper('sizeguide')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('sizeguide/adminhtml_sizeguide_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}