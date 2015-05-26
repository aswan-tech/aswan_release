<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Abstract
{
    protected $_rates;
    protected $_address;

    public function getShippingRates()
    {

        if (empty($this->_rates)) {
            $this->getAddress()->collectShippingRates()->save();

            $groups = $this->getAddress()->getGroupedAllShippingRates();
            /*
            if (!empty($groups)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter(Mage::app()->getStore()->getPriceFilter(), 'price');

                foreach ($groups as $code => $groupItems) {
                    $groups[$code] = $ratesFilter->filter($groupItems);
                }
            }
            */

            return $this->_rates = $groups;
        }

        return $this->_rates;
    }

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }
	/* this function is added by vikrant */
	public function getPostcode()
    {
        if (empty($this->_postcode)) {
            $this->_postcode = $this->getQuote()->getShippingAddress()->getPostcode();
        }
        return $this->_postcode;
    }
	
	/* function to get state name */
	
	public function getState(){
		if (empty($this->statename)) {
			$state = $this->getQuote()->getShippingAddress()->getRegionId();
			
			$regionModel = Mage::getModel('directory/region')->load($state);
			$stateName = $regionModel->getName();
			$this->_statename = $stateName;
		}
		return $this->_statename;
	}
	
	public function checkShippingMethod($carrierName)
	{
		/* extra code added to handle the name fix made */
		$carrierName = explode("_",$carrierName);
		
		if(isset($carrierName[0])){
			$carrierName = $carrierName[0];
			$collection = Mage::getModel('zipcodeimport/zipcodeimport')
					->getCollection()->addFieldToFilter($carrierName, Array('eq'=>1))
					->addFieldToFilter('zip_code', Array('eq'=>$this->getPostcode()));
			return $collection;
		}else{
			return array();
		}
	}
	public function isCustomShipping($carrierName)
	{
		/* extra code added to handle the name fix made */
		$carrierName = explode("_",$carrierName);
		
		if(isset($carrierName[0])){
			$carrierName = $carrierName[0];
			
			$read =  Mage::getSingleton('core/resource')->getConnection('core_read');
			$query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='fcm_zipcodeimport' AND COLUMN_NAME ='".strtolower($carrierName)."'";
			$result = $read->fetchAll($query);
			if(count($result) < 1)
			return false;
			else
			return true;
		}else{
			return false;
		}
	}
    public function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/'.$carrierCode.'/title')) {
            return $name;
        }
        return $carrierCode;
    }

    public function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    public function getShippingPrice($price, $flag)
    {
        return $this->getQuote()->getStore()->convertPrice(Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress()), true);
    }
}
