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


class MLogix_Gallery_Block_Manage_Comment_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                    'method' => 'post',
                ));
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('comment_form', array('legend' => Mage::helper('gallery')->__('Comment Information')));

        $fieldset->addField('user', 'text', array(
            'label' => Mage::helper('gallery')->__('User'),
            'name' => 'user',
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('gallery')->__('Email Address'),
            'name' => 'email',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('gallery')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('gallery')->__('Unapproved'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('gallery')->__('Approved'),
                ),
            ),
        ));

        $fieldset->addField('comment', 'editor', array(
            'name' => 'comment',
            'label' => Mage::helper('gallery')->__('Comment'),
            'title' => Mage::helper('gallery')->__('Comment'),
            'style' => 'width:700px; height:500px;',
            'wysiwyg' => false,
            'required' => false,
        ));

        if (Mage::getSingleton('adminhtml/session')->getGalleryData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getGalleryData());
            Mage::getSingleton('adminhtml/session')->setGalleryData(null);
        } elseif (Mage::registry('gallery_data')) {
            $form->setValues(Mage::registry('gallery_data')->getData());
        }
        return parent::_prepareForm();
    }

}
