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

class MLogix_Gallery_Block_Adminhtml_Week_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
   private $_isGallery = 0;
   private $_addLookItem = false;
   
	public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'adminhtml_week';
        
        $this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Item'));
        //$this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
		
		if( Mage::registry('week_data') ) {
			if (Mage::registry('week_data')->getParentId() != "" && Mage::registry('week_data')->getId() == "" ) {
				$this->_addLookItem = true;
			}
			
			if (Mage::registry('week_data')->getParentId() != "" && Mage::registry('week_data')->getParentId() == 0) {
				$this->_isGallery = 1;
			}
		}
		
    }
    
    public function getFormHtml()
    {
		//$html = "<div id=\"messages\">";
		//$html .= $this->getMessagesBlock()->getGroupedHtml();
		//$html .= "</div>";
    	
    	$html = $this->getChildHtml('gallery_edit_tabs');
    	$js = "<script type=\"javascript\">";	
    	$js .= "if(week_tabsJsTabs) week_tabsJsTabs.showTabContent(week_tabsJsTabs.tabs[0]);";
    	$js .= "editForm = new varienForm('edit_form', '');";   
    	$js .= "</script>";
    	
    	return $html . parent::getFormHtml(). $js;
    }    

    public function getHeaderText()
    {
        if( Mage::registry('week_data') && Mage::registry('week_data')->getId() ) {
			
			if ($this->_isGallery != 0) {
				return Mage::helper('gallery')->__("Edit Look of the Week '%s'", $this->htmlEscape(Mage::registry('week_data')->getTitle()));
			} else {		
				return Mage::helper('gallery')->__("Edit Look Item '%s'", $this->htmlEscape(Mage::registry('week_data')->getTitle()));
			}
        } else {
           if ($this->_addLookItem) {
				return Mage::helper('gallery')->__('Add Look Item');
			} else {
				return Mage::helper('gallery')->__('Add Look of the Weeek');
			}
        }
    }
}