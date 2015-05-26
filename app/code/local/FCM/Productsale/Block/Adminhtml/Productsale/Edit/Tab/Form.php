<?php

class FCM_Productsale_Block_Adminhtml_Productsale_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('productsale_form', array('legend'=>Mage::helper('productsale')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('productsale')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('productsale')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('productsale')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('productsale')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('productsale')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('productsale')->__('Content'),
          'title'     => Mage::helper('productsale')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getProductsaleData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getProductsaleData());
          Mage::getSingleton('adminhtml/session')->setProductsaleData(null);
      } elseif ( Mage::registry('productsale_data') ) {
          $form->setValues(Mage::registry('productsale_data')->getData());
      }
      return parent::_prepareForm();
  }
}