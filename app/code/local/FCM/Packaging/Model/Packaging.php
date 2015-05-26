<?php

/**
 * Magento Model for Premium Packaging
 *
 * 
 *
 * @category    FCM
 * @package     FCM_Packaging
 * @author		Vishal Verma
 * @author_id	51427958
 * @company		HCL Technologies
 * @created 	Wednesday, October 30, 2013
 * @copyright	Four cross media
 */


class FCM_Packaging_Model_Packaging extends Mage_Core_Model_Abstract {
	
	public function bool_isPremiumPackaging($categoryIds, $check_active = false) {
		
		$isPremiumPackaging = false;
		/*
		$isActive	=	true;
		
		foreach($categoryIds as $categoryId) {
			$category = Mage::getModel('catalog/category')
						->setStoreId(Mage::app()->getStore()->getId())
						->load($categoryId);
			
			if($check_active == true){
				$isActive = $category->getIsActive();
			}
			
			$categoryKey = $category->getUrlKey();

			if ($categoryKey == 'premium-packaging' && $isActive) {
				$isPremiumPackaging = true;
				break;
			}
		}
		*/
		return $isPremiumPackaging;
	}
	
	public function checkPremiumPackagingType($premium_pkg_param) {
		/*
		$pre_product = Mage::getModel('catalog/product')->load($premium_pkg_param);
		
		if(strtolower($pre_product->getTypeId() != 'simple')) {
			Mage::getSingleton('checkout/session')->addError(Mage::helper('checkout')->__('Requested premium should be of Simple Type.'));
			$url = Mage::getSingleton('checkout/session')->getRedirectUrl(true);
			if ($url) {
				$this->getResponse()->setRedirect($url);
			} else {
				$this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
			}
		}
		*/
	}
}