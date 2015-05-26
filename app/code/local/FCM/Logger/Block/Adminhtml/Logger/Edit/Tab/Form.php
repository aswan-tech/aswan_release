<?php

class FCM_Logger_Block_Adminhtml_Logger_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('logger_form', array('legend'=>Mage::helper('logger')->__('Item information')));
     
      $fieldset->addField('logger_id', 'label', array(
          'label'     => Mage::helper('logger')->__('ID'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'logger_id',
      ));

      $fieldset->addField('log_time', 'label', array(
          'label'     => Mage::helper('logger')->__('Time'),
          'required'  => false,
          'name'      => 'log_time',
	  ));
	  
	  $fieldset->addField('module_name', 'label', array(
          'label'     => Mage::helper('logger')->__('Module Name'),
          'required'  => false,
          'name'      => 'module_name',
	  ));
	  
	  $fieldset->addType('content', 'FCM_Logger_Varien_Data_Form_Element_Content');	  
	  $fieldset->addField('description', 'content', array(
          'label'     => Mage::helper('logger')->__('Description'),
          'required'  => false,
          'name'      => 'description',
	  ));
	  
	  $fieldset->addField('filename', 'label', array(
          'label'     => Mage::helper('logger')->__('Filename'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'label', array(
          'label'     => Mage::helper('logger')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 'Exception',
                  'label'     => Mage::helper('logger')->__('Exception'),
              ),

              array(
                  'value'     => 'Error',
                  'label'     => Mage::helper('logger')->__('Error'),
              ),
			  array(
                  'value'     => 'Warning',
                  'label'     => Mage::helper('logger')->__('Warning'),
              ),
			  array(
                  'value'     => 'Success',
                  'label'     => Mage::helper('logger')->__('Success'),
              ),
			  array(
                  'value'     => 'Failure',
                  'label'     => Mage::helper('logger')->__('Failure'),
              ),
			  array(
                  'value'     => 'Information',
                  'label'     => Mage::helper('logger')->__('Information'),
              ),
          ),
      ));
     
     /* $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('logger')->__('Content'),
          'title'     => Mage::helper('logger')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));*/
     
      if ( Mage::getSingleton('adminhtml/session')->getLoggerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getLoggerData());
          Mage::getSingleton('adminhtml/session')->setLoggerData(null);
      } elseif ( Mage::registry('logger_data') ) {
          $form->setValues(Mage::registry('logger_data')->getData());
      }
      return parent::_prepareForm();
  }
}