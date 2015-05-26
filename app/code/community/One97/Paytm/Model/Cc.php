<?php


class One97_paytm_Model_Cc extends Mage_Payment_Model_Method_Abstract

{	
	//unique internal payment method identifier
	
	
	protected $_code = 'paytm_cc';
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_paymentMethod			= 'cc';
    protected $_defaultLocale			= 'en';
    protected $_liveUrl	= NULL;
    protected $_formBlockType = 'paytm/form';
    protected $_infoBlockType = 'paytm/info';
    protected $_order;
	
    
    //Get order model
    
	 
    public function getOrder()
    {
		if (!$this->_order) {
			$this->_order = $this->getInfoInstance()->getOrder();
		}
		return $this->_order;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('paytm/processing/redirect');
    }

   
    // Return payment method type string
     
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }

    public function getUrl()
    {
    		if(Mage::getStoreConfig('payment/paytm_cc/mode')==1)
			$this->_liveUrl = Mage::helper('paytm/Data2')->PAYTM_PAYMENT_URL_PROD;
			else
			$this->_liveUrl = Mage::helper('paytm/Data2')->PAYTM_PAYMENT_URL_TEST;
			return $this->_liveUrl;
    }

    


    
    //prepare params array to send it to gateway page via POST
    public function getFormFields()
    {
		
		$price      = number_format($this->getOrder()->getGrandTotal(),2,'.','');
        $currency   = $this->getOrder()->getOrderCurrencyCode();
 		$locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
		if (is_array($locale) && !empty($locale))
			$locale = $locale[0];
		else
			$locale = $this->getDefaultLocale();
		 
		
		$const = (string)Mage::getConfig()->getNode('global/crypt/key');// Mage::getStoreConfig('payment/paytm_cc/constpaytm');
		$mer = Mage::helper('paytm')->decrypt_e($this->getConfigData('inst_key'),$const);
		$merid = Mage::helper('paytm')->decrypt_e($this->getConfigData('inst_id'),$const);
		$website = $this->getConfigData('website');
		$industry_type = $this->getConfigData('industrytype');
		
		
		$lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
		$order = Mage::getSingleton('sales/order');
		$order->load($lastOrderId);
		$_totalData = $order->getData();
		$email = $_totalData['customer_email'];
		$telephone = $order->getBillingAddress()->getTelephone();
		//create array using which checksum is calculated
    	$params = 	array(
	    				'MID' =>	$merid,  				
	    				'TXN_AMOUNT' =>	$price,
    					'CHANNEL_ID' => "WEB",
						'INDUSTRY_TYPE_ID' => $industry_type,
						'WEBSITE' => $website,
						'CUST_ID' => Mage::getSingleton('customer/session')->getCustomer()->getId(),
						'ORDER_ID'	=>	$this->getOrder()->getRealOrderId()    				    
					);
				if($this->getConfigData('send_email'))
					$params['EMAIL'] = $email;
				if($this->getConfigData('send_mobile_no'))
					$params['MOBILE_NO'] = $telephone;
			
					//generate customer id in case this is a guest checkout
				if(!$params['CUST_ID'])
				{
                $resource = Mage::getSingleton('core/resource');
                $read= $resource->getConnection('core_read');
				$query = "SELECT * FROM paytm ORDER BY paytm_id DESC";
                $data = $read->fetchAll($query);
				if(!$data)
				{
					//if table is empty initially
					$resource = Mage::getSingleton('core/resource');
					$write= $resource->getConnection('core_write');
					$write->query("insert into paytm (cust_id) values('1g')");
					$params['CUST_ID']='1g';
				}
				else
				{
					//get last max id,generate new id and insert in table
					$resource = Mage::getSingleton('core/resource');
					$read= $resource->getConnection('core_read');
					$write= $resource->getConnection('core_write');
					$data = $read->fetchAll($query);
					$maxid = $data[0]['paytm_id'];
					$newid = ((int)$maxid)+1;
					$query_str = "insert into paytm (cust_id) values('".$newid."g')";
					$write->query($query_str);
					$params['CUST_ID']=$newid."g";
				}
			}	
		if($this->getConfigData('enable_checksum')==1)
		{
			$checksum = Mage::helper('paytm')->getChecksumFromArray($params, $mer);//generate checksum
			$params['CHECKSUMHASH'] = $checksum;
		}
        return $params;
    }

    protected function _debug($debugData)
    {
        if (method_exists($this, 'getDebugFlag')) {
            return parent::_debug($debugData);
        }

        if ($this->getConfigData('debug')) {
            Mage::log($debugData, null, 'payment_' . $this->getCode() . '.log', true);
        }
    }
}
