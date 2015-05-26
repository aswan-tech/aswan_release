<?php
/**
 * FCM ZipCodeImport Module 
 *
 * Module for importing zip code, city and state for address verification.
 *
 * @category    FCM
 * @package     FCM_ZipCodeImport
 * @author	Vikrant Kumar Mishra
 * @author_id	51402601
 * @company	HCL Technologies
 * @created Thursday, June 5, 2012
 */
 
 
/* Mage Code Checkout cart has been overriden in order to show Shandard and Express shipping
in cart */
class FCM_Shippingmethod_CartController extends Mage_Core_Controller_Front_Action
{
	public function estimatePostAction()
    {
	 /*
		get all shipping detail from shipping address in checkout modules
	 */
        $country    = (string) $this->getRequest()->getParam('country_id');
        $postcode   = (string) $this->getRequest()->getParam('estimate_postcode');
        $city       = (string) $this->getRequest()->getParam('estimate_city');
        $regionId   = (string) $this->getRequest()->getParam('region_id');
        $region     = (string) $this->getRequest()->getParam('region');

        $this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        $this->_getQuote()->save();
		
		// Find if our shipping has been included.
			$rates = $address->collectShippingRates()
						->getGroupedAllShippingRates();
			$qualifies = false;
			foreach ($rates as $carrier) {
				foreach ($carrier as $rate) {
					if ($rate->getMethod() === 'standard') {
						$qualifies = true;
						break;
					}
				}
			}
        $this->_goBack();
    }
}