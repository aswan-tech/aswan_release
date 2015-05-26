<?php

/**
 * PayU_Order_Model_Cron
 *
 * Order Status Update CRON class for Magento
 *
 * PHP versions 5.x
 *
 * PayU India
 * Copyright PayU India (http://payu.in)
 *
 * Redistributions of files is strictly prohibited.
 * 
 * @copyright     Copyright PayU India
 * @link          http://payu.in
 * @package       Model
 * @license       PayU India (http://payu.in)
 */
class PayU_Order_Model_Cron {

    /**
     * PayU Merchant key
     *
     * @var string
     * @access private
     */
    private $_merchant_key;

    /**
     * PayU Merchant salt
     *
     * @var string
     * @access private
     */
    private $_merchant_salt;

    /**
     * PayU WsURL
     *
     * @var string
     * @access private
     */
    private $_ws_url;

    /**
     * Command Parameter for PayU WsURL
     *
     * @var string
     * @access private
     */
    private $_command;

    /**
     * Query String for CURL request
     *
     * @var string
     * @access private
     */
    private $_query_string;

    /**
     * Current Magento Order Id
     *
     * @var string
     * @access private
     */
    private $_order_id;

    /**
     * CURL response for on http request
     *
     * @var string
     * @access private
     */
    private $_output;

    /**
     * Update Order Status
     *
     * @param null
     * @return null
     * @access public
     */
    public function updateOderStatus() {

        
        //Magento Order Collection Model
        $orderCollection = Mage::getResourceModel('sales/order_collection');
		$time = time();
	    $to = date('Y-m-d H:i:s', $time-3600);
	    $lastTime = $time - 604800; // 60*60*24
	    $from = date('Y-m-d H:i:s', $lastTime);
        //Fetching the Orders
        $orderCollection
                ->addFieldToFilter('state', 'new') // Where state is new
                ->addFieldToFilter('status', 'pending') // And status is pending
                ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))				
                ->getSelect();
        
        foreach ($orderCollection->getItems() as $order) {
            
            //Magento Order Model			
            $orderModel = Mage::getModel('sales/order');
            //Loading current order into memory by its id
            $orderModel->load($order->getId());
            $increment_id = $order->getIncrementId();
            $payu_order_id = $increment_id;
            //Fetching the MihPayId for the current upprocessed order
           
            $verifyPayment = $this->verifyPayment($payu_order_id)->toArray();

           

            $payu_order_id = '' . $payu_order_id;
            $count = count($verifyPayment['transaction_details'][$payu_order_id]);
            $index_count = array_filter($verifyPayment['transaction_details'][$payu_order_id], 'is_array');

            
            if ($count == 0 && count($index_count) == 0)
                continue;

            
            elseif (isset($verifyPayment['status']) && $verifyPayment['status'] == 1) {
                
                if (count($index_count) == 0) {
                    
                    if ($verifyPayment['transaction_details'][$payu_order_id]['unmappedstatus'] == 'captured') {
                    
                        //Set the new status for the current order
                        $orderModel->setStatus('Processing');
                        //Save the current order
                        $orderModel->save();
                    } elseif (($verifyPayment['transaction_details'][$payu_order_id]['unmappedstatus'] == 'userCancelled') {
                        
                        //Set the new status for the current order
                        $orderModel->setStatus('canceled');
                        //Save the current order
                        $orderModel->save();
                    } else {
                        
                        $orderModel->setStatus('holded');
                        //Save the current order
                        $orderModel->save();
                    }
                } else {
                    
                    
                    foreach ($verifyPayment['transaction_details'][$payu_order_id] as $key => $value) {
                        //Check if payment is done for the current Order
                        
                        if ($verifyPayment['transaction_details'][$payu_order_id][$key]['unmappedstatus'] == 'captured') {
                            //Set the new status for the current order
                            
                            $orderModel->setStatus('Processing');
                            //Save the current order
                            $orderModel->save();
                            break;
                        } elseif (($verifyPayment['transaction_details'][$payu_order_id][$key]['unmappedstatus'] == 'userCancelled') {
                            //Set the new status for the current order
                            $orderModel->setStatus('canceled');
                         
                            //Save the current order
                            $orderModel->save();
                        } else {
                            
                            $orderModel->setStatus('holded');
                            //Save the current order
                            $orderModel->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * Verify Payment
     *
     * @param String Current Magento Order Id
     * @return object
     * @access public
     */
    public function verifyPayment($order_id) {
        //Set the command varibale to verify payment API
        $this->_command = 'verify_payment';
        //Making HTTP request to PayU Verification service
        $this->_output = $this->_initData()
                ->_prepareData($order_id)
                ->_curlExecute();
        return $this;
    }

    /**
     * Check Payment
     *
     * @param String Current PayU Id (MihPayId)
     * @return object
     * @access public
     */
    public function checkPayment($mihpayid) {
        //Set the command varibale to verify payment API
        $this->_command = 'check_payment';
        //Making HTTP request to PayU Verification service
        $this->_output = $this->_initData()
                ->_prepareData($mihpayid)
                ->_curlExecute();
        return $this;
    }

    /**
     * Initialize Data
     *
     * @param null
     * @return object
     * @access private
     */
    private function _initData() {
        //Setting Merchnat Key
        $this->_merchant_key = Mage::getStoreConfig('payment/payucheckout_shared/key');		
        //Setting Merchnat Salt
        $this->_merchant_salt = Mage::getStoreConfig('payment/payucheckout_shared/salt');
        //Setting Ws URL
        $this->_ws_url = $this->_get_ws_url();
        return $this;
    }

    /**
     * Prepare Data
     *
     * @param String Current Magento Order Id or PayU MihPayId
     * @return object
     * @access private
     */
    private function _prepareData($order_id) {
        //Hash String
        $hash_str = $this->_merchant_key . '|' . $this->_command . '|' . $order_id . '|' . $this->_merchant_salt;
        // Hash
        $hash = strtolower(hash('sha512', $hash_str));
        //Request Parameters
        $r = array('key' => $this->_merchant_key, 'command' => $this->_command, 'hash' => $hash, 'var1' => $order_id);
        //Request Parameter http query string
        $this->_query_string = http_build_query($r);
        return $this;
    }

    /**
     * CURL Execute
     *
     * @param null
     * @return object
     * @access private
     */
    private function _curlExecute() {
        //Initalize CURL
        $c = curl_init();
        //Seting CURL Header to make a http request
        curl_setopt($c, CURLOPT_URL, $this->_ws_url);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $this->_query_string);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        //Executing CURL
        $o = curl_exec($c);
        //Check if CURL have any error
        if (curl_errno($c)) {
            $sad = curl_error($c);
            throw new Exception($sad);
        }
        //Closing CURL connection
        curl_close($c);
        return $o;
    }

    /**
     * Json to Array
     *
     * @param null
     * @return array
     * @access private
     */
    public function toArray() {
        //Json to Array
        return json_decode(json_encode(json_decode($this->_output)), true);
    }

    /**
     * Get Ws URL
     *
     * @param null
     * @return string
     * @access private
     */
    private function _get_ws_url() {
        //PayU getway mode configuration
        $mode = Mage::getStoreConfig('payment/payucheckout_shared/demo_mode');
        if ($mode == '') {
            return "https://info.payu.in/merchant/postservice?form=2";
        } else {
            return "https://test.payu.in/merchant/postservice?form=2";
        }
    }

}