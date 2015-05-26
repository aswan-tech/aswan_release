<?php

class Brandammo_Progallery_Block_Adminhtml_Progallery_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('progallery_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('progallery')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('progallery')->__('Item Information'),
          'title'     => Mage::helper('progallery')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('progallery/adminhtml_progallery_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}