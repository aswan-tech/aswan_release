<?php

class Custom_Mycheckout_Model_Observer{
	
	public function updateShortlists($observer) {
		$product 	= $observer->getEvent()->getProduct();
		$request 	= $observer->getEvent()->getRequest()->getParams();
		$response 	= $observer->getEvent()->getResponse();
		
		$session = Mage::getSingleton('customer/session', array("name" => "frontend"));
		$sessData = @unserialize($session->getData("shortlistedProducts"));
		
		//Mage::log($request);
		//Mage::log($sessData);
		
		//Product added to the cart, delete this from shortlist
		if (($key = array_search($request['product'], $sessData)) !== false) {
			Mage::log("prod found with key ".$key.".....deleting now from session");
			unset($sessData[$key]);
			$session->setData("shortlistedProducts", serialize($sessData));
			//Mage::log($sessData);
		}
	}
}
