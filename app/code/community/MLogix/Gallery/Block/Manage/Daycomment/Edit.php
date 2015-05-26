<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blog
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */


class MLogix_Gallery_Block_Manage_Daycomment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'gallery';
        $this->_controller = 'manage_daycomment';

        $this->_updateButton('save', 'label', Mage::helper('gallery')->__('Save Comment'));
        $this->_updateButton('delete', 'label', Mage::helper('gallery')->__('Delete Comment'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('gallery_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'gallery_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'gallery_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('gallery_data') && Mage::registry('gallery_data')->getId()) {
            return Mage::helper('gallery')->__("Edit Comment By '%s'", $this->htmlEscape(Mage::registry('gallery_data')->getUser()));
        } else {
            return Mage::helper('gallery')->__('Add Comment');
        }
    }

}
