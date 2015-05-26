<?php
/**
 * Magic Logix Gallery
 *
 * Provides an image gallery extension for Magento
 *
 * @category		MLogix
 * @package		Gallery
 * @author		Brady Matthews
 * @copyright		Copyright (c) 2008 - 2010, Magic Logix, Inc.
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/us/
 * @link		http://www.magiclogix.com
 * @link		http://www.magentoadvisor.com
 * @since		Version 1.0
 *
 * Please feel free to modify or distribute this as you like,
 * so long as it's for noncommercial purposes and any
 * copies or modifications keep this comment block intact
 *
 * If you would like to use this for commercial purposes,
 * please contact me at brady@magiclogix.com
 *
 * For any feedback, comments, or questions, please post
 * it on my blog at http://www.magentoadvisor.com/plugins/gallery/
 *
 */
?><?php

class MLogix_Gallery_Block_Adminhtml_Week_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    private $_isGallery = 0;
	private $_isEdit = 0;
	private $_addLookItem = false;
	
	protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('week_form', array('legend' => Mage::helper('gallery')->__('Item information')));

        //$categories = $model->getCategories(0, 0);
		if( Mage::registry('week_data') ) {
			if (Mage::registry('week_data')->getParentId() != "" && Mage::registry('week_data')->getId() == "" ) {
				$this->_addLookItem = true;
			}
			
			if( Mage::registry('week_data') && Mage::registry('week_data')->getId() ) {
				$this->_isEdit = 1;
			}
			
			if (Mage::registry('week_data')->getParentId() != "" && Mage::registry('week_data')->getParentId() == 0) {
				$this->_isGallery = 1;
			}
		}

/*		
		if( Mage::registry('week_data') && Mage::registry('week_data')->getId() ) {
			$this->_isEdit = 1;
			
			$id = Mage::registry('week_data')->getId();
			$gallery = $model->load($id);
			
			$parentId = $gallery->getParentId();
			
			if ($parentId == 0) {
				$this->_isGallery = 1;
			}
		} else {
			$this->_isGallery = 1;
		}
*/
        $ac = array();
/*
		if ($this->_isGallery == 1) {
			$ac[0] = array('value' => 0, 'label' => Mage::helper('gallery')->__('Look of the Week'));
		} else {
			$category = $model->load($parentId);;
			$ac[] = array('value' => $category['gallery_id'], 'label' => Mage::helper('gallery')->__($category['item_title']));
		}
*/
	
	$ac[0] = array('value' => 0, 'label' => Mage::helper('gallery')->__('Look of the Week'));
	
	$model = Mage::getModel('gallery/week');
	
	if (($this->_isGallery != 1 and $this->_addLookItem) OR ($this->_isGallery != 1 and $this->_isEdit)) {		
		$categories = $model->getCategories(0, 0);
		foreach ($categories as $key => $category) {
			$ac[] = array('value' => $category['gallery_id'], 'label' => Mage::helper('gallery')->__($category['item_title']));
		}
	}

		$parent =  $fieldset->addField('parent_id', 'select', array(
            'label' => Mage::helper('gallery')->__('Parent'),
            'required' => true,
            'name' => 'parent_id',
            'id' => 'parent_id',
            'values' => $ac
        ));		
		
		$parent->setAfterElementHtml("<script type=\"text/javascript\">
				$('parent_id').observe('focus', function(event) {
				  this.defaultIndex=this.selectedIndex;
				});
				
				$('parent_id').observe('click', function(event) {
				  this.selectedIndex=this.defaultIndex;
				});
			</script>");
		
		if ($this->_isEdit == 1) {
			$fieldset->addField('item_title', 'text', array(
				'label' => Mage::helper('gallery')->__('Title'),
				'class' => 'required-entry',
				'required' => true,
				'name' => 'item_title',
				'readonly' => true
			));
		}	


/*		
		$fieldset->addField('width', 'text', array(
			'label' => Mage::helper('gallery')->__('Width'),
			'class' => 'required-entry',
			'id' => 'width',
			'required' => false,
			'name' => 'width',
			'readonly' => true
		));
		
		$fieldset->addField('height', 'text', array(
			'label' => Mage::helper('gallery')->__('Height'),
			'class' => 'required-entry',
			'required' => false,
			'id' => 'height',
			'name' => 'height',
			'readonly' => true
		));	
*/
		
		if ($this->_addLookItem) {
			$fieldset->addField('position_no', 'text', array(
				'label' => Mage::helper('gallery')->__('Position No'),
				'required' => false,
				'id' => 'position_no',
				'name' => 'position_no',
			));
		} elseif ($this->_isEdit == 1 && $this->_isGallery) {
			$fieldset->addField('position_no', 'text', array(
				'label' => Mage::helper('gallery')->__('Position No'),
				'required' => false,
				'id' => 'position_no',
				'name' => 'position_no',
				'readonly' => true
			));
		} elseif ($this->_isEdit == 1) {
			$fieldset->addField('position_no', 'text', array(
				'label' => Mage::helper('gallery')->__('Position No'),
				'required' => false,
				'id' => 'position_no',
				'name' => 'position_no',
			));	
		}
		
		$fieldset->addField('heading', 'text', array(
			'label' => Mage::helper('gallery')->__('Heading'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'heading',
		));

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('gallery')->__('Description'),
            'required' => false,
            'name' => 'description',
        ));

        //if ($this->_isEdit == 1 and $this->_isGallery != 1) {
		$fieldset->addField('filename', 'file', array(
			'label' => Mage::helper('gallery')->__('File'),
			'required' => false,
			'name' => 'filename',
		));

		$galleryId = $this->getRequest()->getParam('id');
		if ($galleryId) {
			$galleryModel = $model->load($galleryId);

			$filename = $galleryModel->getFilename();
			//$mediaUrl = $galleryModel->getMediaUrl();
			$thumbUrl = $model->getArchiveThumbUrl();
			
			if ($filename) {
				$fieldset->addField('img', 'note', array(
					'label' => 'Image',
					'required' => false,
					'text' => '<img src="' . $thumbUrl . '"/>'
				));
			}
		}

		$fieldset->addField('alt', 'text', array(
			'label' => Mage::helper('gallery')->__('Alt'),
			'required' => false,
			'name' => 'alt',
		));
		
		$fieldset->addField('tags', 'text', array(
			'label' => Mage::helper('gallery')->__('Tags'),
			'required' => false,
			'name' => 'tags',
		));
	
		if ((! $this->_addLookItem and $this->_isEdit == 0) OR $this->_isGallery == 1) {	
			$fieldset->addField('related_products_sku', 'text', array(
				'label' => Mage::helper('gallery')->__('Related Products SKU'),
				'required' => false,
				'name' => 'related_products_sku'
			));
		}			
		//}

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('gallery')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('gallery')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('gallery')->__('Disabled'),
                ),
            ),
        ));

		if ($this->_isEdit == 0 and ! $this->_addLookItem) {
			$fieldset->addField('created_time', 'date', array(
				'label'     => Mage::helper('gallery')->__('Creation Date'),
				'name'	 => 'created_time',
				'image' => $this->getSkinUrl('images/grid-cal.gif'),
				'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) 
			));
		}
		
        if (Mage::getSingleton('adminhtml/session')->getWeekData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getWeekData());
            Mage::getSingleton('adminhtml/session')->setWeekData(null);
        } elseif (Mage::registry('week_data')) {
            $form->setValues(Mage::registry('week_data')->getData());
        }
        return parent::_prepareForm();
    }

}

