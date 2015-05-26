<?php

/**
 * FCM Zip Code Import Module 
 *
 * Module for importing zip code, city and state for address verification.
 *
 * @category    FCM
 * @package     FCM_Zipcodeimport
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 7, 2012
 */
class FCM_Zipcodeimport_Block_Zipcodeimport extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getZipcodeimport() {
        if (!$this->hasData('zipcodeimport')) {
            $this->setData('zipcodeimport', Mage::registry('zipcodeimport'));
        }
        return $this->getData('zipcodeimport');
    }

}