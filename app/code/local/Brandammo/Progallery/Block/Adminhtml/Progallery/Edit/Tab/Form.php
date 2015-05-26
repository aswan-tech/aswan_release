<?php

class Brandammo_Progallery_Block_Adminhtml_Progallery_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('progallery_form', array('legend'=>Mage::helper('progallery')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('progallery')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('progallery')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('progallery')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('progallery')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('progallery')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('progallery')->__('Content'),
          'title'     => Mage::helper('progallery')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getProgalleryData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getProgalleryData());
          Mage::getSingleton('adminhtml/session')->setProgalleryData(null);
      } elseif ( Mage::registry('progallery_data') ) {
          $form->setValues(Mage::registry('progallery_data')->getData());
      }
      return parent::_prepareForm();
  }
}