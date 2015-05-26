<?php

class Magestore_Departmentimages_Block_Adminhtml_Departmentimages_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('departmentimages_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('departmentimages')->__('Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('departmentimages')->__('General Information'),
          'title'     => Mage::helper('departmentimages')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('departmentimages/adminhtml_departmentimages_edit_tab_form')->toHtml(),
      ));
	 /* 
	  $this->addTab('display_section',array(
			'label'		=> Mage::helper('departmentimages')->__('Categories'),
			'url'       => $this->getUrl('*//*categories', array('_current' => true)),
            'class'     => 'ajax',
	  ));
     */
      return parent::_beforeToHtml();
  }
}