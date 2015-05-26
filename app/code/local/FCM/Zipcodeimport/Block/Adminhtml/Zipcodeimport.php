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
class FCM_Zipcodeimport_Block_Adminhtml_Zipcodeimport extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_zipcodeimport';
        $this->_blockGroup = 'zipcodeimport';
        $this->_headerText = Mage::helper('zipcodeimport')->__('Zip Code Manager');        
        parent::__construct();
        $this->_removeButton('add');
    }

}