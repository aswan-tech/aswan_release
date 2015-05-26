<?php

class Magestore_Bannerslider_Block_Adminhtml_Bannerslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bannerslider_form', array('legend'=>Mage::helper('bannerslider')->__('General information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('bannerslider')->__('Title'),
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

      $fieldset->addField('filename', 'video', array(
          'label'     => Mage::helper('bannerslider')->__('Image File/Video File'),
		  'class'     => 'required-entry',
          'required'  => true,		  
		 /* 'note'  => 'Upload Image of <b>750 * 575</b> (Width * Height) for Best Results. <br/> Only these formats are supported : <br/> jpg, jpeg, png, gif, flv, mp4, webm, ogv.', */
		 'note'  => 'Upload Image of <b>750 * 575</b> (Width * Height) for Best Results. <br/> Only these formats are supported : <br/> jpg, jpeg, png, gif, flv, mp4.',
          'name'      => 'filename',
	  ));
			
	  /* $fieldset->addField('is_home', 'select', array(
          'label'     => Mage::helper('bannerslider')->__('Show in'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'is_home',
		  'values'	=> Mage::helper('bannerslider')->getDisplayOption(),
      )); */
	  
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('bannerslider')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('bannerslider')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('bannerslider')->__('Disabled'),
              ),
          ),
      ));
			
			$fieldset->addField('weblink', 'text', array(
          'label'     => Mage::helper('bannerslider')->__('Web Url'),
          'required'  => false,
          'name'      => 'weblink',
      ));
	  
	  $fieldset->addField('sort_id', 'text', array(
          'label'     => Mage::helper('departmentimages')->__('Sort Id'),
          'required'  => true,
		  'note'  => '<strong>(Should be Same as of home page department Banners Sort Id for Proper Association.)<br/>(For eg : 1-9)</strong>',
          'name'      => 'sort_id',
      ));
		  	 
      $fieldset->addField('preview', 'image', array(
          'label'  => Mage::helper('bannerslider')->__('Preview Image'),
          'required'  => false,
		   'note' => '<strong>(For Video Files Only )</strong>
		   <br/> Only these formats are supported : <br/> jpg,
		   jpeg, png, gif.',
          'name' => 'preview',
	  ));	
     
      if ( Mage::getSingleton('adminhtml/session')->getBannerSliderData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getBannerSliderData();
          Mage::getSingleton('adminhtml/session')->setBannerSliderData(null);
      } elseif ( Mage::registry('bannerslider_data') ) {
          $data = Mage::registry('bannerslider_data')->getData();
      }
	  if(!empty($data['stores'])) {
	  $data['store_id'] = explode(',',$data['stores']);
	  }
	  $form->setValues($data);
	  
      return parent::_prepareForm();
  }
}