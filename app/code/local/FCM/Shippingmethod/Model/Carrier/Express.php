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
class FCM_Shippingmethod_Model_Carrier_Express
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'express';
    protected $_isFixed = true;

    /**
     * Enter description here...
     *
     * @param FCM_Shipping_Model_Rate_Request $data
     * @return FCM_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
		/* It will check whether Express shipping method is enable or not
		  if not it will return to calling function without executing further
		*/
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
			// Below line will check whether the product in question is virtual one or not
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }
			// Below line will be executed whether item in cart are shipped seperatly or not
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        $this->setFreeBoxes($freeBoxes);

        $result = Mage::getModel('shipping/rate_result');
		// this one check whether shipping will be calculated per order wise
        if ($this->getConfigData('type') == 'O') { 
            $shippingPrice = $this->getConfigData('price');
        }
		// this one check whether shipping will be calculated per item wise
		elseif ($this->getConfigData('type') == 'I') { 
            $shippingPrice = ($request->getPackageQty() * $this->getConfigData('price')) - ($this->getFreeBoxes() * $this->getConfigData('price'));
        } else {
            $shippingPrice = false;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false) {
            $method = Mage::getModel('shipping/rate_result_method');
			//Set Shipping Carrier name as Express
            $method->setCarrier('express');
			//Fetch Express Shipping title which we have set from admin
            $method->setCarrierTitle($this->getConfigData('title'));
			//Set Shipping method name as express
            $method->setMethod('express');
			//Fetch Express Shipping name which we have set from admin
            $method->setMethodTitle($this->getConfigData('name'));
			/*
				Check whether any free shipping promotion is being applied or not
				if free shipping applied, below condition make the shipping price as 0
			*/
            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }
			/*
				set shipping Price and Cost as mention in admin configuration or Zero if any free shipping 
				promotion rule is applied
			*/

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
			/* Append this shipping method with other shipping method which are in Enable state */
            $result->append($method);
        }

        return $result;
    }

    public function getAllowedMethods()
    {
		//It will return express shipping in cart page
        return array('express'=>$this->getConfigData('name'));
    }
	
	public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
    {
		$_postcode = $request->getDestPostcode();
		
		$carrier_code = $this->_code;
		
		if(isset($carrier_code)){
			$collection = Mage::getModel('zipcodeimport/zipcodeimport')->getCollection()->addFieldToFilter($carrier_code, Array('eq'=>1))->addFieldToFilter('zip_code', Array('eq'=>$_postcode));
			if (count($collection) < 1){
				return false;
			}else{
				return $this;
			}
		}else{
			return false;
		}
    }
}
