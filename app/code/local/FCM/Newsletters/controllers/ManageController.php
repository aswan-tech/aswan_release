<?php
require_once 'Mage/Newsletter/controllers/ManageController.php';

class FCM_Newsletters_ManageController extends Mage_Newsletter_ManageController{
	//below function overridden just to change the messgae from "The subscription has been saved" to "The account information has been saved"
	public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            Mage::getSingleton('customer/session')->getCustomer()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
            ->save();
            if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The account information has been saved.'));
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__('The account information has been saved.'));
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
        $this->_redirect('customer/account/edit');
    }
	
}
?>