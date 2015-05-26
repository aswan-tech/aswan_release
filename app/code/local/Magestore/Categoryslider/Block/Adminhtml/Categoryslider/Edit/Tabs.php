<?php

class Magestore_Categoryslider_Block_Adminhtml_Categoryslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('categoryslider_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('categoryslider')->__('Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('categoryslider')->__('General Information'),
          'title'     => Mage::helper('categoryslider')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('categoryslider/adminhtml_categoryslider_edit_tab_form')->toHtml(),
      ));
	 /* 
	  $this->addTab('display_section',array(
			'label'		=> Mage::helper('categoryslider')->__('Categories'),
			'url'       => $this->getUrl('*//*categories', array('_current' => true)),
            'class'     => 'ajax',
	  ));
     */
      return parent::_beforeToHtml();
  }
}