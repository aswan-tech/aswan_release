<?php

class Payu_PayuCheckout_Model_Shared extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'payucheckout_shared';
    protected $_isGateway = false;
    protected $_canAuthorize = false;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = false;
    protected $_formBlockType = 'payucheckout/shared_form';
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
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
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
        return Mage::getUrl('payucheckout/shared/redirect');
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields() {

        $billing = $this->getOrder()->getBillingAddress();
        $shipping = $this->getOrder()->getShippingAddress();
        $addr_entity_id = $shipping->getCustomerAddressId();
        $shippingaddress = Mage::getModel('sales/order_address');
        $shippingaddress->load($addr_entity_id);
        $shipaddgetdat = $shippingaddress->getData();



        $biladdr_entity_id = $billing->getCustomerAddressId();
        ;
        $billingaddress = Mage::getModel('sales/order_address');
        $billingaddress->load($biladdr_entity_id);
        $billaddgetdat = $billingaddress->getData();

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


        $key = Mage::getStoreConfig('payment/payucheckout_shared/key');
        $salt = Mage::getStoreConfig('payment/payucheckout_shared/salt');
        $debug_mode = Mage::getStoreConfig('payment/payucheckout_shared/debug_mode');

        $orderid = $this->getOrder()->getRealOrderId();
        $orderInfo = $this->getOrder();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderid);
// get order total value
        $orderValue = number_format($order->getGrandTotal(), 2, '.', $thousands_sep = '');
// get order item collection
        $orderItems = $order->getItemsCollection();
        $productInfo = array();
        $productInfo2 = array();
      
        
        foreach ($orderItems as $item) {
       
            $item->getName();
            $product_id = $item->product_id;
            $product_sku = $item->sku;
            $product_name = $item->getName();
            $_product = Mage::getModel('catalog/product')->load($product_id);
            $cats = $_product->getCategoryIds();
            $category_id = $cats[0]; // just grab the first id
            $category = Mage::getModel('catalog/category')->load($category_id);
            $category_name = $category->getName();

            $productInfo['name'] = $this->cleanString($item->getName());
            $productInfo['description'] = $this->cleanString(substr($_product->getDescription(),0,100));
            $productInfo['value'] = $orderValue;
            $productInfo['isRequired'] = true;
            $productInfo['settlementEvent'] = "EmailConfirmation";
            $productInfo2[] = $productInfo;
        }
        $productIndoFilterData['paymentParts'] = $productInfo2;
        $jsonProductInfo = json_encode($productIndoFilterData);

        $txnid = $orderid;

        $coFields['key'] = $key;
        $coFields['txnid'] = $txnid;
        $coFields['udf2'] = $txnid;
        $coFields['amount'] = number_format($this->getOrder()->getBaseGrandTotal(), 0, '', '');
        $coFields['productinfo'] = $jsonProductInfo;
        $coFields['address'] = $billaddgetdat['street'];
        $coFields['firstname'] = $billing->getFirstname();
        $coFields['Lastname'] = $billing->getLastname();
        $coFields['City'] = $billing->getCity();
        $coFields['State'] = $billing->getRegion();
        $coFields['Country'] = $billing->getCountry();
        $coFields['Zipcode'] = $billing->getPostcode();
        $coFields['email'] = $this->getOrder()->getCustomerEmail();
        $coFields['phone'] = $billing->getTelephone();

        $coFields['ship_name'] = $shipping->getFirstname() . " " . $shipping->getLastname();
        $coFields['ship_address'] = $shipaddgetdat['street'];
        $coFields['ship_zipcode'] = $shipping->getPostcode();
        $coFields['ship_city'] = $shipping->getCity();
        $coFields['ship_state'] = $shipping->getRegion();
        $coFields['ship_country'] = $shipping->getCountry();
        $coFields['ship_phone'] = $shipping->getTelephone();
        $coFields['website'] = Mage::getBaseUrl();
        $coFields['surl'] = Mage::getBaseUrl() . 'payucheckout/shared/success/';
        $coFields['furl'] = Mage::getBaseUrl() . 'payucheckout/shared/failure/';
        $coFields['curl'] = Mage::getBaseUrl() . 'payucheckout/shared/canceled/id/' . $this->getOrder()->getRealOrderId();
        $coFields['Pg'] = $billing->getpg();
        $coFields['bankcode'] = $billing->getbankcode();
        $coFields['ccnum'] = $billing->getccnum();
        $coFields['ccvv'] = $billing->getccvv();
        $coFields['ccexpmon'] = $billing->getccexpmon();
        $coFields['ccexpyr'] = $billing->getccexpyr();
        $coFields['ccname'] = $billing->getccname();
        $coFields['service_provider'] = 'payu_paisa';

        $debugId = '';
      

        if ($debug_mode == 1) {

            $requestInfo = $key . '|' . $coFields['txnid'] . '|' . $coFields['amount'] . '|' .
                    $jsonProductInfo . '|' . $coFields['firstname'] . '|' . $coFields['email'] . '|' . $debugId . '||||||||||' . $salt;
            $debug = Mage::getModel('payucheckout/api_debug')
                    ->setRequestBody($requestInfo)
                    ->save();

            $debugId = $debug->getId();

            $coFields['udf1'] = $debugId;
            $coFields['Hash'] = hash('sha512', $key . '|' . $coFields['txnid'] . '|' . $coFields['amount'] . '|' .
                    $jsonProductInfo . '|' . $coFields['firstname'] . '|' . $coFields['email'] . '|' . $debugId . '|' . $coFields['udf2'] . '|||||||||' . $salt);
        } else {
            $coFields['Hash'] = strtolower(hash('sha512', $key . '|' . $coFields['txnid'] . '|' . $coFields['amount'] . '|' .
                            $jsonProductInfo . '|' . $coFields['firstname'] . '|' . $coFields['email'] . '||' . $coFields['udf2'] . '|||||||||' . $salt));
        }
        return $coFields;
    }

    /**
     * Get url of Payu payment
     *
     * @return string
     */
    public function getPayuCheckoutSharedUrl() {
        $mode = Mage::getStoreConfig('payment/payucheckout_shared/demo_mode');

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

        $order = Mage::getModel('sales/order');
        $debug_mode = Mage::getStoreConfig('payment/payucheckout_shared/debug_mode');
        $key = Mage::getStoreConfig('payment/payucheckout_shared/key');
        $salt = Mage::getStoreConfig('payment/payucheckout_shared/salt');

        if (isset($response['status'])) {
            $txnid = $response['txnid'];
            $orderid = $response['udf2'];
            if ($response['status'] == 'success') {

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
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                    $order->save();
                    $order->sendNewOrderEmail();
                } else {

                    $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
                    $order->cancel()->save();
                }

                if ($debug_mode == 1) {
                    $debugId = $response['udf1'];
                    $data = array('response_body' => implode(",", $response));
                    $model = Mage::getModel('payucheckout/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                }
            }

            if ($response['status'] == 'failure') {
                $order->loadByIncrementId($orderid);
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
                // Inventory updated 
                $this->updateInventory($orderid);

                $order->cancel()->save();

                if ($debug_mode == 1) {
                    $debugId = $response['udf1'];
                    $data = array('response_body' => implode(",", $response));
                    $model = Mage::getModel('payucheckout/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                }
            } else if ($response['status'] == 'pending') {
                $order->loadByIncrementId($orderid);
                $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
                // Inventory updated  
                $this->updateInventory($orderid);
                $order->cancel()->save();

                if ($debug_mode == 1) {
                    $debugId = $response['udf1'];
                    $data = array('response_body' => implode(",", $response));
                    $model = Mage::getModel('payucheckout/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                }
            }
        } else {

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