<?php
/**
 * Magento Model to define the Order supporting functions 
 *
 * This model defines the functions to provide data to order feeds.
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Thursday, June 5, 2012
 * @copyright	Four cross media
 */

/**
 * Order model class
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
class FCM_Fulfillment_Model_Order extends Mage_Core_Model_Abstract 
{
	/**
     * Returns the name of the website, store and store view the order was placed in.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return String The name of the website, store and store view the order was placed in
     */
    protected function getStoreName($order) 
    {
        $storeId = $order->getStoreId();
        if (is_null($storeId)) {
            return $this->getOrder()->getStoreName();
        }
        $store = Mage::app()->getStore($storeId);
        $name = array(
        $store->getWebsite()->getName(),
        $store->getGroup()->getName(),
        $store->getName()
        );
        return implode(', ', $name);
    }
	/**
     * Returns the payment method of the given order.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return String The name of the payment method
     */
    protected function getPaymentMethod($order)
    {
        return $order->getPayment()->getMethod();
    }
    
    /**
     * Returns the shipping method of the given order.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return String The name of the shipping method
     */
    protected function getShippingMethod($order)
    {
        if (!$order->getIsVirtual() && $order->getShippingMethod()) {
            return $order->getShippingMethod();
        }
        return '';
    }
    
    /**
     * Returns the total quantity of ordered items of the given order.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return int The total quantity of ordered items
     */
    protected function getTotalQtyItemsOrdered($order) {
        $qty = 0;
        $orderedItems = $order->getItemsCollection();
        foreach ($orderedItems as $item)
        {
            if (!$item->isDummy()) {
                $qty += (int)$item->getQtyOrdered();
            }
        }
        return $qty;
    }

    /**
     * Returns the sku of the given item dependant on the product type.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return String The sku
     */
    protected function getItemSku($item)
    {
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $item->getProductOptionByCode('simple_sku');
        }
        return $item->getSku();
    }

    /**
     * Returns the options of the given item separated by comma(s) like this:
     * option1: value1, option2: value2
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return String The item options
     */
    protected function getItemOptions($item)
    {
        $options = '';
        if ($orderOptions = $this->getItemOrderOptions($item)) {
            foreach ($orderOptions as $_option) {
                if (strlen($options) > 0) {
                    $options .= ', ';
                }
                $options .= $_option['label'].': '.$_option['value'];
            }
        }
        return $options;
    }

    /**
     * Returns all the product options of the given item including additional_options and
     * attributes_info.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return Array The item options
     */
    protected function getItemOrderOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }
	
	/*
	 * getCatalogDiscountAmount is used to calculate catalog discount amount
	 * @param $item Object
	 * @return Numeric 
	 */
	  
	protected function getOriginalPriceTotal($item) 
    {
       $qty_ordered = (int)$item->getQtyOrdered();
       $catalogDiscount = $this->getCatalogDiscountAmount($item) / $qty_ordered;
       if($catalogDiscount) {
		   return ($item->getProductMrp()) - $catalogDiscount;
	   }
	   else {
		   return $item->getProductMrp();
	   }
   }
    
	/*
	 * getCatalogDiscountAmount is used to calculate catalog discount amount
	 * @param $item Object
	 * @return Numeric 
	 */
	  
	protected function getCatalogDiscountAmount($item) 
    {
       $qty_ordered = (int)$item->getQtyOrdered();
       return (((int)$item->getProductMrp()*$qty_ordered) - ($item->getRowTotal()+ $item->getTaxAmount() + $item->getHiddenTaxAmount() + $item->getWeeeTaxAppliedRowAmount()));
    }
    
    /**
     * Calculates and returns the grand total of an item including tax and excluding
     * discount.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return Float The grand total
     */
    protected function getItemTotal($item) 
    {
        //return $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount();
		return $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount() + $item->getWeeeTaxAppliedRowAmount() - $item->getDiscountAmount();
    }

    /**
     * This function has been modified to include discount value in row total
     */
    protected function getItemTotalWithoutDiscount($item) 
    {
        //return $item->getRowTotal() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount() + $item->getHiddenTaxAmount();
        return $item->getRowTotalInclTax();
    }

    /**
     * Formats a price by adding the currency symbol and formatting the number 
     * depending on the current locale.
     *
     * @param Float $price The price to format
     * @param Mage_Sales_Model_Order $formatter The order to format the price by implementing the method formatPriceTxt($price)
     * @return String The formatted price
     */
    protected function formatPrice($price, $formatter) 
    {
        return $formatter->formatPriceTxt($price);
    }
	
	/**
     * Return the order data in an array for easy processing 
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
	 
	protected function getOrderData($order) 
	{
		$data = array();
		
		$shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
		
		$data['OrderNumber'] = $order->getRealOrderId();
		//$data['OrderCreated'] = Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', true);
		//$data['OrderUpdated'] = Mage::helper('core')->formatDate($order->getUpdatedAt(), 'medium', true);
		//$data['OrderCreated'] = $order->getCreatedAt();
		//$data['OrderUpdated'] = $order->getUpdatedAt();
		
		$dateCreated = Mage::app()->getLocale()->date(strtotime($order->getCreatedAt()), null, null);
		$dateCreated = $dateCreated->toString('yyyy-MM-dd HH:mm:ss');
	
		$dateUpdated = Mage::app()->getLocale()->date(strtotime($order->getUpdatedAt()), null, null);
		$dateUpdated = $dateUpdated->toString('yyyy-MM-dd HH:mm:ss');
		
		$data['OrderCreated'] = $dateCreated;
		$data['OrderUpdated'] = $dateUpdated;
		
		$data['Status'] = $order->getStatus();
		$data['PurchasedFrom'] = $this->getStoreName($order);
		if(strtolower($this->getPaymentMethod($order)) == 'paytm_cc') {
			$data['PaymentMethod'] = 'Paytm-wallet';
		}
		else if(strtolower($this->getPaymentMethod($order)) == 'payumoney_shared') {
			$data['PaymentMethod'] = 'Payumoney-wallet';
		}
		else{
			$data['PaymentMethod'] = ($this->getPaymentMethod($order) == "free" ? 'prepaid' : $this->getPaymentMethod($order));
		}
		$data['ShippingMethod'] = $this->getShippingMethod($order);
		
		//Shipping Provider
		$data['ShippingProvider'] = $order->getData('blinkecarrier_id');
		
		//$data['ExpectedDeliveryDate'] = $order->getData('shipping_arrival_date');
		
		$expectedDelivery = $order->getData('shipping_arrival_date');
		
		if (is_null($expectedDelivery)) {
			$dateDelivery = "";
		} else {
			$dateDelivery = Mage::app()->getLocale()->date(strtotime($order->getData('shipping_arrival_date')), null, null);
			$dateDelivery = $dateDelivery->toString('yyyy-MM-dd 00:00:00');
		}
		$data['ExpectedDeliveryDate'] = $dateDelivery;
		
		
		$timeSlot = $order->getData('shipping_time_slot');
		if (is_null($timeSlot)) {
			$timeSlot = "";
		}
		$data['ExpectedTimeSlot'] = $timeSlot;
		
		$data['Currency'] = $order->getOrderCurrencyCode();
		$data['ExchangeRate'] = $order->getBaseToOrderRate();
		
		$data['Subtotal'] = $order->getData('subtotal');
		$data['Tax'] = $order->getData('tax_amount');
		
		//VAT is for Delhi and CST for other states
		if ($shippingAddress->getRegionCode() == 'IN-DL') {
			$data['TaxCategory'] = 'VAT';
		} else {
			$data['TaxCategory'] = 'CST';
		}		
		
		$data['ShippingAmount'] = $order->getData('shipping_amount');
		$data['Discount'] = $order->getData('discount_amount');
		
		$data['ShippingDiscountAmount'] = $order->getData('shipping_discount_amount');
		$data['RewardsDiscountAmount'] = $order->getData('rewards_discount_amount');
		
		
		$data['GrandTotal'] = $order->getData('grand_total');
		$data['TotalPaid'] = $order->getData('total_paid');
		$data['TotalRefunded'] = $order->getData('total_refunded');
		$data['TotalDue'] = $order->getData('total_due');
		
		$data['TotalInvoiced'] = $this->getTotalQtyItemsOrdered($order);
		$data['TotalQtyItemsOrdered'] = $this->getTotalQtyItemsOrdered($order);
		$data['Weight'] = $order->getWeight();
		$data['CustomerName'] = $order->getCustomerName();
		$data['CustomerFirstName'] = $order->getCustomerFirstname();
		$data['CustomerLastName'] = $order->getCustomerLastname();
		$data['CustomerMiddleName'] = $order->getCustomerMiddlename();
		$data['CustomerEmail'] =  $order->getCustomerEmail();
		
		//Billing Address
		$data['BillToTitle'] = $billingAddress->getPrefix();
		$data['BillToName'] = $billingAddress->getName();
		$data['BillToFirstName'] = $billingAddress->getFirstname();
		$data['BillToLastName'] = $billingAddress->getLastname();
		$data['BillToMiddleName'] = $billingAddress->getMiddlename();
		$data['BillToAddressStreet'] = $billingAddress->getData("street");
		$data['BillToCity'] = $billingAddress->getData("city");
		$data['BillToRegionCode'] = $billingAddress->getRegionCode();
		$data['BillToRegion'] = $billingAddress->getRegion();
		$data['BillToCountry'] = $billingAddress->getCountry();
		$data['BillToCountryName'] = $billingAddress->getCountryModel()->getName();
		$data['BillToPostalCode'] = $billingAddress->getData("postcode");
		
		$billPhoneNumber = $billingAddress->getData("telephone");
		$data['BillToCustomerPhoneNum'] = $billPhoneNumber;
		$data['BillToEmail'] = $billingAddress->getData("email");
	
			
		//Shipping Address
		$data['ShipToTitle'] = $shippingAddress ? $shippingAddress->getPrefix() : '';
		$data['ShipToName'] = $shippingAddress ? $shippingAddress->getName() : '';
		$data['ShipToFirstName'] = $shippingAddress ? $shippingAddress->getFirstname() : '';
		$data['ShipToLastName'] = $shippingAddress ? $shippingAddress->getLastname() : '';
		$data['ShipToMiddleName'] = $shippingAddress ? $shippingAddress->getMiddlename() : '';
		$data['ShipToAddressStreet'] = $shippingAddress ? $shippingAddress->getData("street") : '';
		$data['ShipToCity'] = $shippingAddress ? $shippingAddress->getData("city") : '';
		$data['ShipToRegionCode'] = $shippingAddress ? $shippingAddress->getRegionCode() : '';
		$data['ShipToRegion'] = $shippingAddress ? $shippingAddress->getRegion() : '';
		$data['ShipToCountry'] = $shippingAddress ? $shippingAddress->getCountry() : '';
		$data['ShipToCountryName'] = $shippingAddress ? $shippingAddress->getCountryModel()->getName() : '';
		$data['ShipToPostalCode'] = $shippingAddress ? $shippingAddress->getData("postcode") : '';
		
		$shipPhoneNumber = $shippingAddress ? $shippingAddress->getData("telephone") : '';
		
		if (!empty($shipPhoneNumber) and $shipPhoneNumber != '-') {
			$data['ShipToCustomerPhoneNum'] = $shipPhoneNumber;
		} else {
			$data['ShipToCustomerPhoneNum'] = $billPhoneNumber;
		}
		
		$data['ShipToEmail'] = $shippingAddress ? $shippingAddress->getData("email") : '';
		if($this->getPaymentMethod($order) == 'free')
		{
			$orderDetails = $order->getData();
			//check for store credit or gv
            if(isset($orderDetails['customer_balance_amount']) && $orderDetails['customer_balance_amount'] > 0){
				 $data['PaymentType'] = 'StoreCredit';
	        }
			else
			{
	        	        if($order->getGiftCardsAmount() > 0) {
					$data['PaymentType'] = 'GiftVoucherCode';
	        	        }
			}
		}
		else if(strtolower($this->getPaymentMethod($order)) == 'paytm_cc') {
			$data['PaymentType'] = 'Paytm-wallet';
		}
		else if(strtolower($this->getPaymentMethod($order)) == 'payumoney_shared') {
			$data['PaymentType'] = 'Payumoney-wallet';
		}
		else
		{
			$data['PaymentType'] = ($this->getPaymentMethod($order) == 'cashondelivery' ? 'COD' : $this->getPaymentMethod($order));
		}
		return $data;
	}
	
	/**
     * Return the order items data in an array for easy processing 
     *
	 * @param Mage_Sales_Model_Order_Item $item
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
	 
	protected function getOrderItemData($item, $order) 
	{
		$data = array();
		
		/*
		 * get applied coupon type on each iems
		 */
		  
		$rule =  Mage::getModel('salesrule/rule')->load($item->getData('applied_rule_ids'), 'rule_id');
		
		$data['ItemId'] = $item->getItemId();
		$data['Name'] = $item->getName();
		$data['Status'] = $item->getStatus();
		$data['SKU'] = $this->getItemSku($item);
		$data['productId'] = $this->getOrderItemProductId($item->getItemId());		
		$data['ProductType'] = $this->getOrderItemType($item->getItemId());
		$data['Options'] = $this->getItemOptions($item);
		$data['MRP'] = $item->getData('product_mrp');
		$data['OriginalPrice'] = $this->getOriginalPriceTotal($item);
		$data['Price'] = $item->getData('price');
		
		$data['QtyOrdered'] = (int)$item->getQtyOrdered();
		$data['QtyBackordered'] = (int)$item->getQtyBackordered();
		$data['QtyInvoiced'] = (int)$item->getQtyInvoiced();
		$data['QtyShipped'] = (int)$item->getQtyShipped();
		$data['QtyCanceled'] = (int)$item->getQtyCanceled();
		$data['QtyRefunded'] = (int)$item->getQtyRefunded();
		$data['Weight'] = $item->getWeight();
		$data['TotalWeight'] = $item->getRowWeight();
		
		$data['Tax'] = $item->getTaxAmount();
		$data['TaxPercent'] = $item->getTaxPercent();
				
		if(!empty($rule->getData('voucher_type'))) {
			$data['DiscountType'] = strtoupper($rule->getData('voucher_type'));
		}
		else {
			$data['DiscountType'] = null;
		}
		
		$data['Discount'] = $item->getDiscountAmount();
		$data['CatalogDiscount'] = round($this->getCatalogDiscountAmount($item));
		$data['Total'] = $this->getItemTotal($item);
		
		//Premium Packaging
		$pckOpt = $item->getPckOption();
		if ($pckOpt == 1) {
			$hasPremiumPackaging = "Yes"; 
		} else {
			$hasPremiumPackaging = "No"; 
		}
	
		$data['HasPremiumPackaging'] = $hasPremiumPackaging;
		$data['PremiumSKU'] = $item->getPckSku();
		$data['PremiumQty'] = (int)$item->getPckQty();
	
		return $data;
	}
	
	/**
     * Retrieve store carriers
     *
	 * @param Store Id
     * @return array
     */
    public function getCarriers($orderStoreId)
    {
        $carriers = array();
        $carrierInstances = Mage::getSingleton('shipping/config')->getAllCarriers($orderStoreId);
        $carriers['custom'] = Mage::helper('sales')->__('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }
	
	/**
	* Magento get item id from order id and sku
	*
	* @param int, int
	* @return int
	*/
	public function getOrderItemIdBySku($orderIncrementId, $sku)
	{
		$orderId = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId)->getId();
		
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_read');
		$salesItemTable = $resource->getTableName('sales/order_item');
		
		$query = $connection->select()
							->from($salesItemTable, array('item_id'))
							->where('order_id=?', $orderId)
							->where('sku=?', $sku);    
				
		$itemId = $connection->fetchCol($query);   
		
		return $itemId[0];
	}
	
	/**
	* Magento get item type
	*
	* @param int
	* @return string
	*/
	
	public function getOrderItemType($itemId) {
		$item = Mage::getModel('sales/order_item')->load($itemId);
		$type =	$item->getProductType();
	
		return $type;
	}
	
	/**
	* Magento get item data from order id and sku
	*
	* @param int, int
	* @return array
	*/
	public function getOrderItemDataBySku($orderIncrementId, $sku)
	{
		$orderId = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId)->getId();
		
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_read');
		$salesItemTable = $resource->getTableName('sales/order_item');
	
		$query = $connection->select()
							->from(array('it' => $salesItemTable), array('item_id', 'parent_item_id', 'it.product_type'))
							->joinLeft(array('itp' => $salesItemTable), 'it.parent_item_id=itp.item_id', array('parent_product_type'=>'product_type'))
							->where('it.order_id=?', $orderId)
							->where('it.sku=?', $sku);    
				
		$itemData = $connection->fetchRow($query);   
		
		return $itemData;
	
	}
	
	/*
	 * getActivePaymentMethods() method is used to get all active payment methods, StoreCredit, GiftVoucherCode and GiftVoucherValue
	 * @param $order Object
	 * @return Array
	 */ 
	
	public function getActivePaymentMethods($order)
	{
		$payments = Mage::getSingleton('payment/config')->getActiveMethods();
		$methods = array();
		$currPayMethod = $this->getPaymentMethod($order);
		$orderDetails = $order->getData();
		
		foreach ($payments as $paymentCode=>$paymentModel) {			
			$paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
			if(strtolower($paymentTitle) != 'no payment information required') {
				
				if($paymentCode == 'zaakpay'){
					$methods['Zaakpay'] = ($currPayMethod == 'zaakpay' ? $order->getData('grand_total') : '');
				}
				else if($paymentCode == 'ccavenuepay'){
					$methods['CCAvenue'] = ($currPayMethod == 'ccavenuepay' ? $order->getData('grand_total') : '');
				}
				else if($paymentCode == 'cashondelivery'){
					$methods['CashOnDelivery'] = ($currPayMethod == 'cashondelivery' ? $order->getData('grand_total') : '');
				}
				else if($paymentCode == 'payucheckout_shared'){
					$methods['Payu'] = ($currPayMethod == 'payucheckout_shared' ? $order->getData('grand_total') : '');
				}
				else if($paymentCode == 'payseal_standard'){
					$methods['Payseal'] = ($currPayMethod == 'payseal_standard' ? $order->getData('grand_total') : '');
				}
				else if($paymentCode == 'paytm_cc'){
					$methods['PaytmWallet'] = ($currPayMethod == 'paytm_cc' ? $order->getData('grand_total') : '');
				}
				else if($paymentCode == 'payumoney_shared') {
					$methods['PayumoneyWallet'] = ($currPayMethod == 'payumoney_shared' ? $order->getData('grand_total') : '');
				}
			}
		}
		
		$giftCardsArr = unserialize($order->getGiftCards());
		if(isset($orderDetails['customer_balance_amount']) && $orderDetails['customer_balance_amount'] > 0){
			$methods['StoreCredit'] = $orderDetails['customer_balance_amount'];
		}
		else{
			$methods['StoreCredit'] = null;
		}
		
		$methods['GiftVoucherCode'] = isset($giftCardsArr['c']) ? $giftCardsArr['c'] : null;
		if($order->getGiftCardsAmount() > 0) {
			$methods['GiftVoucherValue'] = $order->getGiftCardsAmount();
		}
		else {
			$methods['GiftVoucherValue'] = null;
		}
		$methods['Coupon'] = $order->getData('discount_amount');
		return $methods;
	}
	
	/*
	 * getCoupons() method is used to get coupon code and value
	 * @param $order Object
	 * @return Array
	 */ 
	
	public function getCoupons($order) {
		//$orderDate = date("Y-m-d", strtotime($order->getData("created_at")));
		$return = array();
        $discountAmtTemp = $order->getData('discount_amount');
		if($discountAmtTemp > 0 && empty($order->getData('coupon_code'))){
				$return['CouponCode'] = "SYSTEM";
				$return['CouponType'] = "DISCOUNT";
				$return['CouponValue'] = $order->getData('discount_amount');
		}else{
			$return['CouponCode'] = $order->getData('coupon_code');

			$rule =  Mage::getModel('salesrule/rule')->load($order->getData('applied_rule_ids'),'rule_id');
			if(!empty($rule->getData('voucher_type'))) {
				$return['CouponType'] = strtoupper($rule->getData('voucher_type'));
			}
			else {
				$return['CouponType'] = null;
			}
			
			$return['CouponValue'] = $order->getData('discount_amount');
		}

		// $return['RewardPointValue'] = $order->getData('reward_points_balance');

		 return $return;
	}
	
	/*
	 * getGiftCardData() method is used to get coupon code and value
	 * @param $item Object
	 * @param $order Object
	 * @return Array
	 */ 
	
	public function getGiftCardData($item, $order) {
		$options = $item->getProductOptions($item);
		$giftcardCodesArr = $options['giftcard_created_codes'];
		$return = array();
		if(is_array($giftcardCodesArr) && count($giftcardCodesArr) > 0) {
			foreach($giftcardCodesArr as $indx=>$value) {
				$return['GvCode-'.$indx] = $value;
			}
		}
		return $return;		
	}
	/*
	 * getOrderItemProductId() method is used to get product id of item
	 * @param $item Object
	 * @return Integer
	 */ 
	 
	public function getOrderItemProductId($itemId) {
		$item = Mage::getModel('sales/order_item')->load($itemId);
		$type =	$item->getProductId();	
		return $type;
	}
}

