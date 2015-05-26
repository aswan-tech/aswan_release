<?php

class Custom_Sizeguide_Block_Adminhtml_Sizeguide_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('sizeguide_form', array('legend'=>Mage::helper('sizeguide')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('sizeguide')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      /* $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('sizeguide')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  )); */
		
     
     
      $fieldset->addField('content_size', 'editor', array(
            'name' => 'content_size',
			'class'     => 'required-entry',
			'required'  => true,
            'label' => Mage::helper('sizeguide')->__('Content'),
            'title' => Mage::helper('sizeguide')->__('Content'),
            'style' => 'height:36em;width:59em',
            'required' => true,
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig()
        ));

	  
	   $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('sizeguide')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('sizeguide')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('sizeguide')->__('Disabled'),
              ),
          ),
      ));
	  
	  $fieldset->addField('order_id', 'text', array(
          'label'     => Mage::helper('sizeguide')->__('Order Id'),
          'required'  => false,
          'name'      => 'order_id',
	  ));
     
      if ( Mage::getSingleton('adminhtml/session')->getSizeGuideData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSizeGuideData());
          Mage::getSingleton('adminhtml/session')->setSizeGuideData(null);
      } elseif ( Mage::registry('sizeguide_data') ) {
          $form->setValues(Mage::registry('sizeguide_data')->getData());
      }
      return parent::_prepareForm();
  }
}