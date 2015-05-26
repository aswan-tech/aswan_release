<?php

class Magestore_Departmentimages_Block_Adminhtml_Departmentimages_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('departmentimages_form', array('legend'=>Mage::helper('departmentimages')->__('General information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('departmentimages')->__('Title'),
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
          'label'     => Mage::helper('departmentimages')->__('Image File'),
		  'class'     => 'required-entry',
          'required'  => true,		  
		  'note'  => 'Upload Image of <b>230 * 95</b> (Width * Height) for Best Results. <br/> Only these formats are supported : jpg,jpeg,png,gif',
          'name'      => 'filename',
	  ));
	  
	   $fieldset->addField('filename_hover', 'image', array(
          'label'     => Mage::helper('departmentimages')->__('Hover Image File'),
		  'class'     => 'required-entry',
          'required'  => true,		  
		  'note'  => 'Upload Image of <b>230 * 95</b> (Width * Height) for Best Results. <br/> Only these formats are supported : jpg,jpeg,png,gif',
          'name'      => 'filename_hover',
	  ));
	  
	  if(Mage::getStoreConfig('departmentimages/settings/show_categories')) {
	  
		$children=Mage::getModel('catalog/category')->load(2)->getChildrenCategories();
		
		/* $cat[]= array('label'=>'Select Category', 'value' =>''); */

			$cat = array();
			
			foreach ($children as $category) {
			
			if(strtolower($category->getUrlKey()!='clearance') && strtolower($category->getUrlKey()!='premium-packaging')) {
			
				$cat[]= array('label'=>ucwords(strtolower($this->escapeHtml($category->getName()))), 'value' =>$category->getId());
			}
			
			}
			
		$fieldset->addField('category', 'select', array(
           'name'      => 'category',
           'label'     => Mage::helper('departmentimages')->__('Select Department'),
           'title'     => Mage::helper('departmentimages')->__('Select Department'),
		   'class'     => 'required-entry',
           'required'  => true,
           'values'    => $cat,
		   // 'style'     => 'height:10em;',		   
		));
		 
		} /* End of if */
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('departmentimages')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('departmentimages')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('departmentimages')->__('Disabled'),
              ),
          ),
      ));
			
		$fieldset->addField('weblink', 'text', array(
          'label'     => Mage::helper('departmentimages')->__('Web Url'),
          'required'  => false,
          'name'      => 'weblink',
      ));
	  
	  $fieldset->addField('sort_id', 'text', array(
          'label'     => Mage::helper('departmentimages')->__('Sort Id'),
          'required'  => true,
		  'note'  => '<strong>(Should be Same as of home page Main Banners Sort Id for Proper Association.)<br/>(For eg : 1-9)</strong>',
          'name'      => 'sort_id',
      ));
			
      /* $fieldset->addField('preview', 'image', array(
          'label'     => Mage::helper('departmentimages')->__('Preview Image'),
          'required'  => false,
		   'note'  => 'Only these formats are supported : jpg,jpeg,png,gif',
          'name'      => 'preview',
	  )); */
			
     
      if ( Mage::getSingleton('adminhtml/session')->getDepartmentImagesData() )
      {
          $data = Mage::getSingleton('adminhtml/session')->getDepartmentImagesData();
          Mage::getSingleton('adminhtml/session')->setDepartmentImagesData(null);
      } elseif ( Mage::registry('departmentimages_data') ) {
          $data = Mage::registry('departmentimages_data')->getData();
      }
	  if(!empty($data['stores'])) {
	  $data['store_id'] = explode(',',$data['stores']);
	  }
	  $form->setValues($data);
	  
      return parent::_prepareForm();
  }
}