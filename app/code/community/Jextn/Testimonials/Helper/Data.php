<?php

class Jextn_Testimonials_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_TITLE   = 'testimonials/testimonials/title';
	const XML_PATH_PUBKEY  = 'testimonials/recaptcha/pubkey';
	const XML_PATH_PRIVKEY = 'testimonials/recaptcha/privkey';
	const XML_PATH_AUTOAPPROVED = 'testimonials/testimonials/autoapproved';
	
	const XML_PATH_SIDEBAR_COUNT   = 'testimonials/testimonials/sidebar';
	const XML_PATH_FOOTER_COUNT   = 'testimonials/testimonials/footer';

	public function getTestimonialsTitle()
	{
		if(trim(Mage::getStoreConfig(self::XML_PATH_TITLE))==''){
			$titletest = $this->__('Testimonials');
		} else {
			$titletest = Mage::getStoreConfig(self::XML_PATH_TITLE);
		}
		return $titletest;
	} 
	
	public function getRecaptcha()
	{	
		$recaptcha = new Zend_Service_ReCaptcha(Mage::getStoreConfig(self::XML_PATH_PUBKEY), Mage::getStoreConfig(self::XML_PATH_PRIVKEY));
		return $recaptcha;
	}
	public function checkRecaptcha()
	{
		return trim(Mage::getStoreConfig(self::XML_PATH_PUBKEY));
	}
	public function getAutoApproved()
	{	
		return Mage::getStoreConfig(self::XML_PATH_AUTOAPPROVED);
	}
	
	public function getSideBarCount()
	{
		return trim(Mage::getStoreConfig(self::XML_PATH_SIDEBAR_COUNT));
	}
	public function getFooterCount()
	{	
		return Mage::getStoreConfig(self::XML_PATH_FOOTER_COUNT);
	}
}