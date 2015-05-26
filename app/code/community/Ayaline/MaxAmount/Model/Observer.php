<?php
/**
 * created : 11/04/2012
 * 
 * @category Ayaline
 * @package Ayaline_MaxAmount
 * @author aYaline
 * @copyright Ayaline - 2012 - http://magento-shop.ayaline.com
 * @license http://shop.ayaline.com/magento/fr/conditions-generales-de-vente.html
 */

/**
 * 
 * @package Ayaline_MaxAmount
 */
class Ayaline_MaxAmount_Model_Observer {

	/**
	 * Check if cart amount is greater than the "max amount" allowed (on cart)
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Ayaline_MaxAmount_Model_Observer
	 */
	public function checkCartMaxAmount(Varien_Event_Observer $observer) {
		/* @var $event Varien_Event */
		$event = $observer->getEvent();
		/* @var $quote Mage_Sales_Model_Quote */
		$quote = $event->getQuote();
		Mage::helper('ayalinemaxamount')->checkCartMaxAmount($quote);
		return $this;
	}

	/**
	 * Check if cart amount is greater than the "max amount" allowed (before add product)
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Ayaline_MaxAmount_Model_Observer
	 */
	public function checkProductMaxAmount(Varien_Event_Observer $observer) {
		/* @var $event Varien_Event */
		$event = $observer->getEvent();
		/* @var $quoteItem Mage_Sales_Model_Quote_Item */
		$quoteItem = $event->getQuoteItem();
		Mage::helper('ayalinemaxamount')->checkProductMaxAmount($quoteItem);
		return $this;
	}


}