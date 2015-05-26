<?php


class Payu_PayuMoney_Model_Shared extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'payumoney_shared';
    protected $_isGateway = false;
    protected $_canAuthorize = false;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseMoney = true;
    protected $_canUseForMultishipping = false;
    protected $_formBlockType = 'payumoney/shared_form';
    protected $_paymentMethod = 'shared';
    protected $_order;

    public function cleanString($string) {

        $string_step1 = strip_tags($string);
        $string_step2 = nl2br($string_step1);
        $string_step3 = str_replace("<br />", "<br>", $string_step2);
        $cleaned_string = str_replace("\"", " inch", $string_step3);
        return $cleaned_string;
    }

    /**
     * Get money session namespace
     *
     * @return Mage_Money_Model_Session
     */
    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote() {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder() {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                    ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }

    public function getCustomerId() {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/customer_id');
    }

    public function getAccepteCurrency() {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/currency');
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('payumoney/shared/redirect');
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields() {

        $billing = $this->getOrder()->getBillingAddress();
        $coFields = array();
        $items = $this->getQuote()->getAllItems();

        if ($items) {
            $i = 1;
            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $coFields['c_prod_' . $i] = $this->cleanString($item->getSku());
                $coFields['c_name_' . $i] = $this->cleanString($item->getName());
                $coFields['c_description_' . $i] = $this->cleanString($item->getDescription());
                $coFields['c_price_' . $i] = number_format($item->getPrice(), 2, '.', '');
                $i++;
            }
        }

        $request = '';
        foreach ($coFields as $k => $v) {
            $request .= '<' . $k . '>' . $v . '</' . $k . '>';
        }


        $key = Mage::getStoreConfig('payment/payumoney_shared/key');
        $salt = Mage::getStoreConfig('payment/payumoney_shared/salt');
        $debug_mode = Mage::getStoreConfig('payment/payumoney_shared/debug_mode');

        $orderId = $this->getOrder()->getRealOrderId();

        $mode=Mage::getStoreConfig('payment/payumoney_shared/demo_mode');
	    if($mode!='')
		{
		  $txnid = $orderId."_".rand(); 
		}
		else 
	    $txnid = $orderId; 

        $coFields['key'] = $key;
        $coFields['txnid'] = $txnid;

        $coFields['amount'] = number_format($this->getOrder()->getBaseGrandTotal(), 0, '', '');
        $coFields['productinfo'] = 'Prpduct Information';
        $coFields['firstname'] = $billing->getFirstname();
        $coFields['Lastname'] = $billing->getLastname();
        $coFields['City'] = $billing->getCity();
        $coFields['State'] = $billing->getRegion();
        $coFields['Country'] = $billing->getCountry();
        $coFields['Zipcode'] = $billing->getPostcode();
        $coFields['email'] = $this->getOrder()->getCustomerEmail();
        $coFields['phone'] = $billing->getTelephone();

        $coFields['surl'] = Mage::getBaseUrl() . 'payumoney/shared/success/';
        $coFields['furl'] = Mage::getBaseUrl() . 'payumoney/shared/failure/';
        //$coFields['curl'] =  Mage::getBaseUrl().'payumoney/shared/canceled/id/'.$this->getOrder()->getRealOrderId();

        $debugId = '';
        $coFields['bankcode'] = "PAYUW";
        $coFields['Pg'] = 'Wallet';



        if ($debug_mode == 1) {

            $requestInfo = $key . '|' . $coFields['txnid'] . '|' . $coFields['amount'] . '|' .
                    $coFields['productinfo'] . '|' . $coFields['firstname'] . '|' . $coFields['email'] . '|' . $debugId . '||||||||||' . $salt;
            $debug = Mage::getModel('payumoney/api_debug')
                    ->setRequestBody($requestInfo)
                    ->save();

            $debugId = $debug->getId();

            $coFields['udf1'] = $debugId;
            $coFields['Hash'] = hash('sha512', $key . '|' . $coFields['txnid'] . '|' . $coFields['amount'] . '|' .
                    $coFields['productinfo'] . '|' . $coFields['firstname'] . '|' . $coFields['email'] . '|' . $debugId . '||||||||||' . $salt);
        } else {
            $coFields['Hash'] = strtolower(hash('sha512', $key . '|' . $coFields['txnid'] . '|' . $coFields['amount'] . '|' .
                            $coFields['productinfo'] . '|' . $coFields['firstname'] . '|' . $coFields['email'] . '|||||||||||' . $salt));
        }
        return $coFields;
    }

    /**
     * Get url of Payu payment
     *
     * @return string
     */
    public function getPayuMoneySharedUrl() {
       $mode = Mage::getStoreConfig('payment/payumoney_shared/demo_mode');
       $url = 'https://test.payu.in/_payment.php';
        if ($mode == '') {
            $url = 'https://secure.payu.in/_payment.php';
        }     

        return $url;
    }

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug() {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }

    public function capture(Varien_Object $payment, $amount) {
        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment) {
        $payment->setStatus(self::STATUS_DECLINED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * parse response POST array from gateway page and return payment status
     *
     * @return bool
     */
    public function parseResponse() {

        return true;
    }

    /**
     * Return redirect block type
     *
     * @return string
     */
    public function getRedirectBlockType() {
        return $this->_redirectBlockType;
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType() {
        return $this->_paymentMethod;
    }

    public function getResponseOperation($response) {
        Mage::log(print_r($response, true), null, $file, true);
        $order = Mage::getModel('sales/order');
        $debug_mode = Mage::getStoreConfig('payment/payumoney_shared/debug_mode');
        $key = Mage::getStoreConfig('payment/payumoney_shared/key');
        $salt = Mage::getStoreConfig('payment/payumoney_shared/salt');
        if (isset($response['status'])) {
            Mage::log("in first if when status is set", null, $file, true);
		    $txnid=$response['txnid'];
		   $mode=Mage::getStoreConfig('payment/payumoney_shared/demo_mode');
	       if($mode!='')
		   {
		     $txnid_split = explode("_", $txnid);
			 $orderid = $txnid_split[0];
		   }
		   else 
		   $orderid=$txnid;
            if ($response['status'] == 'success') {
                Mage::log("in if when mode is COD or status is success", null, $file, true);
                $status = $response['status'];
                $order->loadByIncrementId($orderid);
                $billing = $order->getBillingAddress();
                $amount = $response['amount'];
                $productinfo = $response['productinfo'];
                $firstname = $response['firstname'];
                $email = $response['email'];
                $keyString = '';
                $Udf1 = $response['udf1'];
                $Udf2 = $response['udf2'];
                $Udf3 = $response['udf3'];
                $Udf4 = $response['udf4'];
                $Udf5 = $response['udf5'];
                $Udf6 = $response['udf6'];
                $Udf7 = $response['udf7'];
                $Udf8 = $response['udf8'];
                $Udf9 = $response['udf9'];
                $Udf10 = $response['udf10'];
                if ($debug_mode == 1) {
                    $keyString = $key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email . '|' . $Udf1 . '|' . $Udf2 . '|' . $Udf3 . '|' . $Udf4 . '|' . $Udf5 . '|' . $Udf6 . '|' . $Udf7 . '|' . $Udf8 . '|' . $Udf9 . '|' . $Udf10;
                } else {
                    $keyString = $key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email . '|' . $Udf1 . '|' . $Udf2 . '|' . $Udf3 . '|' . $Udf4 . '|' . $Udf5 . '|' . $Udf6 . '|' . $Udf7 . '|' . $Udf8 . '|' . $Udf9 . '|' . $Udf10;
                }

                $keyArray = explode("|", $keyString);
                $reverseKeyArray = array_reverse($keyArray);
                $reverseKeyString = implode("|", $reverseKeyArray);
                $saltString = $salt . '|' . $status . '|' . $reverseKeyString;
                $sentHashString = strtolower(hash('sha512', $saltString));
                $responseHashString = $_REQUEST['hash'];
                if ($sentHashString == $responseHashString) {
                    Mage::log("in if when hashstring matches", null, $file, true);
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                    $order->save();
                    $order->sendNewOrderEmail();
                } else {
                    Mage::log("in when hashstring not matche", null, $file, true);
                    $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
                    $order->cancel()->save();
                }

                if ($debug_mode == 1) {
                    $debugId = $response['udf1'];
                    $data = array('response_body' => implode(",", $response));
                    $model = Mage::getModel('payumoney/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                }
            }

            if ($response['status'] == 'failure') {
                Mage::log("in if when status is failure", null, $file, true);
                $order->loadByIncrementId($orderid);
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
                // Inventory updated 
                $this->updateInventory($orderid);

                $order->cancel()->save();

                if ($debug_mode == 1) {
                    $debugId = $response['udf1'];
                    $data = array('response_body' => implode(",", $response));
                    $model = Mage::getModel('payumoney/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                }
            } else if ($response['status'] == 'pending') {
                Mage::log("in if when status is pending", null, $file, true);
                $order->loadByIncrementId($orderid);
                $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
                // Inventory updated  
                $this->updateInventory($orderid);
                $order->cancel()->save();

                if ($debug_mode == 1) {
                    $debugId = $response['udf1'];
                    $data = array('response_body' => implode(",", $response));
                    $model = Mage::getModel('payumoney/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                }
            }
        } else {
            Mage::log("In last else ", null, $file, true);
            $order->loadByIncrementId($response['id']);
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
            // Inventory updated 
            $order_id = $response['id'];
            $this->updateInventory($order_id);
            $order->cancel()->save();
        }
    }

    public function updateInventory($order_id) {

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = $order->getAllItems();
        foreach ($items as $itemId => $item) {
            $ordered_quantity = $item->getQtyToInvoice();
            $sku = $item->getSku();
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId())->getQty();

            $updated_inventory = $qtyStock + $ordered_quantity;

            $stockData = $product->getStockItem();
            $stockData->setData('qty', $updated_inventory);
            $stockData->save();
        }
    }

 }