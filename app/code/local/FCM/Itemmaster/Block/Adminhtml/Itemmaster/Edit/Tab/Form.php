<?php

class FCM_Itemmaster_Block_Adminhtml_Itemmaster_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('itemmaster_form', array('legend'=>Mage::helper('itemmaster')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('itemmaster')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('itemmaster')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('itemmaster')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('itemmaster')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('itemmaster')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('itemmaster')->__('Content'),
          'title'     => Mage::helper('itemmaster')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getItemmasterData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getItemmasterData());
          Mage::getSingleton('adminhtml/session')->setItemmasterData(null);
      } elseif ( Mage::registry('itemmaster_data') ) {
          $form->setValues(Mage::registry('itemmaster_data')->getData());
      }
      return parent::_prepareForm();
  }
}