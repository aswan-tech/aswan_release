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
class Ayaline_MaxAmount_Helper_Data extends Mage_Core_Helper_Abstract {

	const XML_MAXAMOUNT_CART_ENABLE = 'ayalinemaxamount/cart/enable';
	const XML_MAXAMOUNT_CART_MAXAMOUNT = 'ayalinemaxamount/cart/maxamount';
	const XML_MAXAMOUNT_CART_MESSAGE = 'ayalinemaxamount/cart/message';

	const XML_MAXAMOUNT_PRODUCT_ENABLE = 'ayalinemaxamount/product/enable';
	const XML_MAXAMOUNT_PRODUCT_MESSAGE = 'ayalinemaxamount/product/message';

	/* CONFIGURATIONS */
	
	/**
	 * Check cart max amount is enabled
	 *
	 * @param mixed $store
	 * @return bool
	 */
	public function isCartEnable($store = null) {
		return Mage::getStoreConfig(self::XML_MAXAMOUNT_CART_ENABLE, $store);
	}

	/**
	 * Check if product max amount is enabled
	 *
	 * @param mixed $store
	 * @return bool
	 */
	public function isProductEnable($store = null) {
		if($this->isCartEnable($store)) {
			return Mage::getStoreConfig(self::XML_MAXAMOUNT_PRODUCT_ENABLE, $store);
		}
		return false;
	}

	/**
	 * Retrieve cart max amount
	 *
	 * @param mixed $store
	 * @return float
	 */
	public function getCartMaxAmount($store = null) {
		return Mage::getStoreConfig(self::XML_MAXAMOUNT_CART_MAXAMOUNT, $store);
	}

	/**
	 * Retrieve cart message
	 *
	 * @param mixed $store
	 * @return string
	 */
	public function getCartMessage($store = null) {
		return Mage::getStoreConfig(self::XML_MAXAMOUNT_CART_MESSAGE, $store);
	}

	/**
	 * Retrieve product view message
	 *
	 * @param mixed $store
	 * @return string
	 */
	public function getProductMessage($store = null) {
		return Mage::getStoreConfig(self::XML_MAXAMOUNT_PRODUCT_MESSAGE, $store);
	}
	
	/**
	 * Check max amount on cart
	 *
	 * @param Mage_Sales_Model_Quote $quote
	 */
	public function checkCartMaxAmount($quote) {
		$quoteStore = $quote->getStore();
		if($this->isCartEnable($quoteStore)) {
			$maxAmount = $this->getCartMaxAmount($quoteStore);
			$grandTotal = $quote->getGrandTotal();
			if($grandTotal > $maxAmount) {
				$formater = new Varien_Filter_Template();
				$formater->setVariables(array('amount' => Mage::helper('core')->currency($maxAmount, true, false)));
				$format = $this->getCartMessage($quoteStore);
				//	hold checkout
				$quote->setHasError(true)->addMessage($formater->filter($format));
			}
		}
	}
	
	/**
	 * Check max amount after adding product to cart
	 *
	 * @param Mage_Sales_Model_Quote_Item $quoteItem
	 */
	public function checkProductmaxAmount($quoteItem){
		$quoteStore = $quoteItem->getStore();
		if($this->isProductEnable($quoteStore)) {
			$maxAmount = $this->getCartMaxAmount($quoteStore);
			/* @var $quote Mage_Sales_Model_Quote */
			$quote = $quoteItem->getQuote();
			$grandTotal = $quote->getGrandTotal();
			
			$_product = Mage::getModel('catalog/product')->load($quoteItem->getProduct()->getId());
			
			$grandTotal += $_product->getFinalPrice($quoteItem->getQty()); 
			if($grandTotal > $maxAmount){
				$formater = new Varien_Filter_Template();
				$formater->setVariables(array('amount' => Mage::helper('core')->currency($maxAmount, true, false)));
				$format = $this->getProductMessage($quoteStore);
				// throw exception for "remove" product to cart
				Mage::throwException($formater->filter($format));
			}
		}
	}
}
