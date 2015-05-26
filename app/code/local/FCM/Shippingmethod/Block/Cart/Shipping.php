<?php
/**
 * FCM Shippingmethod Module 
 *
 * Module for importing zip code, city and state for address verification.
 *
 * @category    FCM
 * @package     FCM_Shippingmethod
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 5, 2012
 */
class FCM_Shippingmethod_Block_Cart_Shipping extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    } 
	
	/**
	* Function to get all enabled shipping methods
	**/
	public function getShippingMethods(){
		
		//$shipMethods = Mage::getConfig()->getNode('default/shipping_methods')->asArray();
		//$shipMethods = array_keys($shipMethods);
		
		$carriers = Mage::getModel('shipping/config')->getActiveCarriers();
		$shippingMethods = array();
		
		foreach($carriers as $k=>$c){
			
			//if(in_array($k, $shipMethods)){
				$shippingMethods[$k]['code'] = $k."_".$k;
				//$shippingMethods[$k]['code'] = $k;
				$shippingMethods[$k]['title'] = Mage::getStoreConfig('carriers/'.$k.'/title');	
				$shippingMethods[$k]['title2'] = Mage::getStoreConfig('carriers/'.$k.'/title2');
				
				$shippingMethods[$k]['temandourl'] = Mage::getStoreConfig('carriers/'.$k.'/temandourl');
				$shippingMethods[$k]['temandokey'] = Mage::getStoreConfig('carriers/'.$k.'/temandokey');
				$shippingMethods[$k]['temandopass'] = Mage::getStoreConfig('carriers/'.$k.'/temandopass');
												
			//}
			
		}
		
		return $shippingMethods;
		
	}
	
	/**
	* Function to get selected shipping method
	**/
	public function getAddressShippingMethod(){
		
		return Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()->getShippingMethod();
		
	}
	
	/**
	* Function to set default shipping method_exists
	**/
	public function setDefaultShippingMethod($method = ""){
		Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()->setShippingMethod($method);
	}
	
	/**
	* Function to check if customer is logged in
	**/
	public function checkIfLoggedIn(){
		
		$customerSession=Mage::getSingleton("customer/session");
		return $customerSession->isLoggedIn();
		
	}
	
	/**
	* Function to get postcode from customer's saved address
	**/
	
	public function getCustomerAddressPostcode(){
		$postcode = "";
		
		if($this->checkIfLoggedIn()){
			
			$customerSession=Mage::getSingleton("customer/session");
			$customerAddress=$customerSession->getCustomer()->getDefaultShippingAddress();
		
			if($customerAddress && $customerAddress->getId()){
				$postcode = $customerAddress->getPostcode();
				Mage::getSingleton('core/session')->setPostcode($postcode);
				//Mage::getModel('shippingmethod/carrier_delivery')->updateShippingAmount();
			}			
		} 
		return $postcode;
	}
	
	/**
	* Function to get saved postcode either from session or from customer address
	**/
	public function getSavedPostcode(){		
		
		$postcode = "";
		
		$postcode = $this->getCustomerAddressPostcode();
		
		if(!$postcode){
		
			$postcode = Mage::getSingleton('core/session')->getPostcode();			
		
		}
		
		return $postcode;
	}
}
?>