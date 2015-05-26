<?php

class Magestore_Categoryslider_Block_Adminhtml_Categoryslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('categoryslider_form', array('legend'=>Mage::helper('categoryslider')->__('General information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('categoryslider')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
			
			/* if (!Mage::app()->isSingleStoreMode()) {
				$fieldset->addField('store_id', 'multiselect', array(
							'name'      => 'stores[]',
							'label'     => Mage::helper('cms')->__('Store View'),
							'title'     => Mage::helper('cms')->__('Store View'),
							'required'  => true,
							'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
					));
			}
			else {
					$fieldset->addField('store_id', 'hidden', array(
							'name'      => 'stores[]',
							'value'     => Mage::app()->getStore(true)->getId()
					));
					$model->setStoreId(Mage::app()->getStore(true)->getId());
			} */

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('categoryslider')->__('Image File'),
		  'class'     => 'required-entry',
          'required'  => true,
		  'note'  => 'Upload Image of <b>750 * 420</b> (Width * Height) for Best Results. <br/> Only these formats are supported : jpg,jpeg,png,gif',
          'name'      => 'filename',
	  ));
	  
		$children=Mage::getModel('catalog/category')->load(2)->getChildrenCategories();
		// $cat[]= array('label'=>'Select Category', 'value' =>'');

			$cat = array();
			
			foreach ($children as $category) {
			
			if(strtolower($category->getUrlKey()!='clearance')){ //"commenting code as this fetaure is being disabled tempriorly" && strtolower($category->getUrlKey()!='premium-packaging')) {
				$cat[]= array('label'=>ucwords(strtolower($this->escapeHtml($category->getName()))), 'value' =>$category->getId());
			}
			
			}
		$fieldset->addField('category', 'select', array(
           'name'      => 'category',
           'label'     => Mage::helper('categoryslider')->__('Select Department'),
           'title'     => Mage::helper('categoryslider')->__('Select Department'),
		   'class'     => 'required-entry',
           'required'  => true,
           'values'    => $cat,
		   // 'style'     => 'height:10em;',		   
		));
			
	  /* $fieldset->addField('is_home', 'select', array(
          'label'     => Mage::helper('categoryslider')->__('Show in'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'is_home',
		  'values'	=> Mage::helper('categoryslider')->getDisplayOption(),
      )); */
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('categoryslider')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('categoryslider')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('categoryslider')->__('Disabled'),
              ),
          ),
      ));
			
		$fieldset->addField('weblink', 'text', array(
          'label'     => Mage::helper('categoryslider')->__('Web Url'),
          'required'  => false,
          'name'      => 'weblink',
      ));
	  
		$fieldset->addField('content', 'textarea', array(
          'label'     => Mage::helper('categoryslider')->__('Content'),
          'required'  => false,
          'name'      => 'content',
      ));
			
      /* $fieldset->addField('preview', 'image', array(
          'label'     => Mage::helper('categoryslider')->__('Preview Image'),
          'required'  => false,
		   'note'  => 'Only these formats are supported : jpg,jpeg,png,gif',
          'name'      => 'preview',
	  )); */
			
     
      if ( Mage::getSingleton('adminhtml/session')->getCategorySliderData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getCategorySliderData();
          Mage::getSingleton('adminhtml/session')->setCategorySliderData(null);
      } elseif ( Mage::registry('categoryslider_data') ) {
          $data = Mage::registry('categoryslider_data')->getData();
      }
	  if(!empty($data['stores'])) {
	  $data['store_id'] = explode(',',$data['stores']);
	  }
	  $form->setValues($data);
	  
      return parent::_prepareForm();
  }
}