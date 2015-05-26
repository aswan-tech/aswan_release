<?php
/**
*************************************************************************************
 Please Do not edit or add any code in this file without permission of bluezeal.in.
@Developed by bluezeal.in

Magento version 1.7.0.2                 CCAvenue Version 1.31
                              
Module Version. bz-1.0                 Module release: September 2012
**************************************************************************************
*//**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category   Mage
 * @package    Mage_ccavenuepay
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



class Mage_Ccavenuepay_Model_Method_Ccavenuepay extends Mage_Payment_Model_Method_Abstract
{
    protected $_formBlockType = 'ccavenuepay/form_ccavenuepay';
    protected $_infoBlockType = 'ccavenuepay/info_ccavenuepay';
    protected $_canSaveCcavenuepay     = false;
	protected $_code  = 'ccavenuepay';
	protected $_canUseInternal          = false;

   
    public function assignData($data)
    {
		if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setCcavenuepayType($this->getCcavenuepayAccountId1())	
			->setMerchant_Id($data->getMerchant_Id())
			->setOrder_Id($data->getOrder_Id())
			->setAmount($data->getAmount())
			->setCurrency_code($data->getCurrency_code())
			->setShipping($data->getShipping())
			->setChecksum($data->getChecksum())
			->setTax($data->getTax())
			->setBilling_cust_name($data->getBilling_cust_name())			
			->setBilling_cust_address($data->getBilling_cust_address())
			->setBilling_cust_city($data->getBilling_cust_city())
			->setBilling_cust_state($data->getBilling_cust_state())
			->setBilling_zip_code($data->getBilling_zip_code())
			->setBilling_cust_country($data->getBilling_cust_country())
			->setBilling_cust_tel($data->getBilling_cust_tel())
			->setbilling_cust_email($data->getbilling_cust_email())
			->setDelivery_cust_name($data->getDelivery_cust_name())
			->setDelivery_cust_address($data->getDelivery_cust_address())
			->setDelivery_cust_city($data->getDelivery_cust_city())
			->setDelivery_cust_state($data->getDelivery_cust_state())
			->setDelivery_zip_code($data->getDelivery_zip_code())
			->setDelivery_cust_country($data->getDelivery_cust_country())
			->setDelivery_cust_tel($data->getDelivery_cust_tel())
			->setBilling_cust_notes($data->getBilling_cust_notes())
			->setRedirect_Url($data->getRedirect_Url());
		
        return $this;
    }

    
    public function prepareSave()
    {
        $info = $this->getInfoInstance();
        if ($this->_canSaveCcavenuepay) {
            $info->setCcavenuepayNumberEnc($info->encrypt($info->getCcavenuepayNumber()));
        }
        $info->setCcavenuepayNumber(null)
            ->setCcavenuepayCid(null);
        return $this;
    }
	public function getProtocolVersion()
    {
        return '1.0';
    }
	
	
    public function getSession()
    {
        return Mage::getSingleton('ccavenuepay/session');
    }

    
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
	
    public function getQuote()
    {
        
	    return $this->getCheckout()->getQuote();
    }
	
	public function getStandardCheckoutFormFields($option = '')
    {
       
	    if ($this->getQuote()->getIsVirtual()) {
            $a = $this->getQuote()->getBillingAddress();
            $b = $this->getQuote()->getShippingAddress();
        } else {
            $a = $this->getQuote()->getShippingAddress();
            $b = $this->getQuote()->getBillingAddress();
        }
		$data=$this->getQuoteData($option);
        $sArr = array(	'Merchant_Id' => $data['Merchant_Id'],
			'Order_Id' => $data['Order_Id'],
			'Amount' => $data['Amount'],
			'currency_code' => 	$data['currency_code'],
			'shipping'=>$data['shipping'],
			'tax'=>$data['tax'],
			'Checksum'=>$data['Checksum'],
			'billing_cust_name' 			=> $data['billing_cust_name'],
			'billing_cust_address'  		=> $data['billing_cust_address'],
			'billing_cust_city' 			=> $data['billing_cust_city'],
			'billing_cust_state'    		=> $data['billing_cust_state'],
			'billing_zip_code'      		=> $data['billing_zip_code'],
			'billing_cust_country'  		=> $data['billing_cust_country'],
			'billing_cust_tel'      		=> $data['billing_cust_tel'],
			'billing_cust_email'    		=> $data['billing_cust_email'],
			'delivery_cust_name'    		=> $data['delivery_cust_name'],
			'delivery_cust_address'     	=> $data['delivery_cust_address'],
			'delivery_cust_city' 			=> $data['delivery_cust_city'],
			'delivery_cust_state'   		=> $data['delivery_cust_state'],
			'delivery_zip_code'     		=> $data['delivery_zip_code'],
			'delivery_cust_country'     	=> $data['delivery_cust_country'],
			'delivery_cust_tel'       		=> $data['delivery_cust_tel'],
			'billing_cust_notes'    		=> $data['billing_cust_notes'],
			'Redirect_Url'              	=> $data['Redirect_Url'],
			);
        $sReq = '';
        $rArr = array();
        foreach ($sArr as $k=>$v) {
           
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }
        return $rArr;
    }

    public function getCcavenuepayUrl()
    {
		 $url=$this->_getCcavenuepayConfig()->getCcavenuepayServerUrl();
         return $url;
    }
	
	
	public function getOrderPlaceRedirectUrl()
    {
	         return Mage::getUrl('ccavenuepay/ccavenuepay/redirect');
    }
	public function getQuoteData($option = '')
    {					
	
		if ($option == 'redirect') {
    		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
    		$quote = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		} else {
			$quote = $this->getQuote();
		}

		$data=array();
				 	
		if ($quote)
		{
			if($quote->getShippingAddress())
			{
				if ($quote->getIsVirtual()) {
					$a = $quote->getBillingAddress();
					$b = $quote->getShippingAddress();
				} else {
					$a = $quote->getShippingAddress();
					$b = $quote->getBillingAddress();
				}
			}
			else
			{
				$a = $quote->getBillingAddress();
				$b = $quote->getBillingAddress();
			}
			
			$MerchantId = Mage::getStoreConfig('payment/ccavenuepay/merchantid');
			$OrderId = $this->getCheckout()->getLastRealOrderId();
			$Amount  = Mage::app()->getStore()->roundPrice($quote->getGrandTotal());
			$Workingkey = Mage::getStoreConfig('payment/ccavenuepay/workingkey');
			$Url = $this->_getCcavenuepayConfig()->getCcavenuepayRedirecturl();
			
			$pattern='http://www.';
			if(!(@Eregi($pattern,$Url,$reg)))
			@eregi_replace('http://', $pattern, $Url);
			$WorkingKey =  Mage::getStoreConfig('payment/ccavenuepay/workingkey');
			
			$str ="$MerchantId|$OrderId|$Amount|$Url|$WorkingKey";
			$adler = 1;
			$BASE =  65521 ;
			
			$s1 = $adler & 0xffff ;
			$s2 = ($adler >> 16) & 0xffff;
			for($i = 0 ; $i < strlen($str) ; $i++)
			{
				$s1 = ($s1 + Ord($str[$i])) % $BASE ;
				$s2 = ($s2 + $s1) % $BASE ;
			
			}
			
			$str = $s2;
			$num = 16;
			$dec ='';
			
			$str = DecBin($str);
			
			for( $i = 0 ; $i < (64 - strlen($str)) ; $i++)
			$str = "0".$str ;
			
			for($i = 0 ; $i < $num ; $i++) 
			{
				$str = $str."0";
				$str = substr($str , 1 ) ;
			}
			$num=$str;
			for ($n = 0 ; $n < strlen($num) ; $n++)
			{
				$temp = $num[$n] ;
				$dec =  $dec + $temp*pow(2 , strlen($num) - $n - 1);
			}
			$Checksum = $dec + $s1;
			$AuthDesc = 'N';
			
			$data['Merchant_Id'] = Mage::getStoreConfig('payment/ccavenuepay/merchantid');
			$data['Order_Id'] = $this->getCheckout()->getLastRealOrderId();
			$data['Amount']  = $Amount;
			$data['currency_code']  = $quote->getBaseCurrencyCode();
			if($quote->getShippingAmount())
			{
				$data['shipping'] = sprintf('%.2f', $quote->getShippingAmount());
			}
			else
			{
				$data['shipping'] = '0';
			}
			$data['tax']      = sprintf('%.2f', $quote->getTaxAmount());
			$data['Checksum']=$Checksum;
			
			if($this->getQuote()->getCustomer())
			{
				$email_id =$this->getQuote()->getCustomer()->getEmail();
			}
			
			$bState = $b->getRegionId();
			$bRegionModel = Mage::getModel('directory/region')->load($bState);
			$bStateName = ucfirst($bRegionModel->getName());
			
			$aState = $a->getRegionId();
			$aRegionModel = Mage::getModel('directory/region')->load($aState);
			$aStateName = ucfirst($aRegionModel->getName());				
			
			$data['billing_cust_name'] 			=$b->getFirstname()." ".$b->getLastname();
			$data['billing_cust_address'] 		=$b->getStreet(1)."   ".$b->getStreet(2);
			$data['billing_cust_city'] 			=$b->getCity();
			//$data['billing_cust_state'] 		=$b->getRegionCode();
			$data['billing_cust_state'] 		=$bStateName;
			$data['billing_zip_code']   		=$b->getPostcode();
			$data['billing_cust_country'] 		=$b->getCountryModel()->getName();
			$data['billing_cust_tel'] 		    =$b->getTelephone();
			$data['billing_cust_email'] 		=$quote->getCustomerEmail();
			$data['delivery_cust_name'] 		=$a->getFirstname()." ".$a->getLastname();
			$data['delivery_cust_address']  	=$a->getStreet(1)."   ".$a->getStreet(2);
			$data['delivery_cust_city']         =$a->getCity();
			//$data['delivery_cust_state'] 		=$a->getRegionCode();
			$data['delivery_cust_state'] 		=$aStateName;
			$data['delivery_zip_code']  		=$a->getPostcode();
			$data['delivery_cust_country']  	= $a->getCountryModel()->getName();
			$data['delivery_cust_tel']   		=$a->getTelephone();
			$data['billing_cust_notes'] 		='';
			$data['Redirect_Url']           	=$this->_getCcavenuepayConfig()->getCcavenuepayRedirecturl();
			}
		 
		return $data; 
	}
	
	public function getchecksum($MerchantId,$Amount,$OrderId ,$URL,$WorkingKey)
	{
		$str ="$MerchantId|$OrderId|$Amount|$URL|$WorkingKey";
		$adler = 1;
		$adler = $this->adler32($adler,$str);
		return $adler;
	}
	
	public function verifychecksum($MerchantId,$OrderId,$Amount,$AuthDesc,$CheckSum,$WorkingKey)
	{
		$str = "$MerchantId|$OrderId|$Amount|$AuthDesc|$WorkingKey";
		$adler = 1;
		$adler = $this->adler32($adler,$str);
		
		if($adler == $CheckSum)
			return "true" ;
		else
			return "false" ;
	}
	
	public function adler32($adler , $str)
	{
		$BASE =  65521 ;
	
		$s1 = $adler & 0xffff ;
		$s2 = ($adler >> 16) & 0xffff;
		for($i = 0 ; $i < strlen($str) ; $i++)
		{
			$s1 = ($s1 + Ord($str[$i])) % $BASE ;
			$s2 = ($s2 + $s1) % $BASE ;
	
		}
		return $this->leftshift($s2 , 16) + $s1;
	}
	
	public function leftshift($str , $num)
	{
	
		$str = DecBin($str);
	
		for( $i = 0 ; $i < (64 - strlen($str)) ; $i++)
			$str = "0".$str ;
	
		for($i = 0 ; $i < $num ; $i++) 
		{
			$str = $str."0";
			$str = substr($str , 1 ) ;
		}
		return $this->cdec($str) ;
	}
	
	public function cdec($num)
	{
		$dec = '';
		for ($n = 0 ; $n < strlen($num) ; $n++)
		{
		   $temp = $num[$n] ;
		   $dec =  $dec + $temp*pow(2 , strlen($num) - $n - 1);
		}
	
		return $dec;
	}

	protected function _getCcavenuepayConfig()
    {
        return Mage::getSingleton('ccavenuepay/config');
    }
	
	public function isAvailable($quote=null)
    {
        if (is_null($quote)) {
           return false;
        }
		$return = parent::isAvailable($quote);
		if($return==false)return false;
				
		return true;
		
    }
}
 