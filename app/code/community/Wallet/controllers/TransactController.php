<?php 



class Wallet_TransactController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Set the quote Id generated in the table into session
     * and add the block of wallet form which will submit itself
     * to the wallet site
     */
    public function redirectAction() 
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setWalletQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('wallet/redirect')->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }
	
	public function showfailure($error)
	{
		// failure/cancel
		error_log('Response entered in failed statement');
		$session = Mage::getSingleton('checkout/session');
		if ($session->getLastRealOrderId()) {
			$order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
			if ($order->getId()) {
				$order->cancel()->save();
			}
		}
		$er = 'Wallet could not process your request because of the error "'.$error . '"'; 
		$session->addError($er);
		$this->_redirect('wallet/transact/failure');
	}

    public function responseAction() 
    {
        // actual processing
        $postdata = Mage::app()->getRequest()->getPost();
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getWalletQuoteId(true));
	    $walletConfig = Mage::getStoreConfig('payment/wallet');

        $statuscode = $postdata['statuscode'];
        $orderid = $postdata['orderid'];
        $mid = $postdata['mid'];
        $amount = $postdata['amount'];
        $message = $postdata['statusmessage'];
        $checksumReceived = $postdata['checksum'];
        
        $all = "'" . $statuscode . "''" . $orderid . "''" . $amount . "''" . $message . "''" . $mid . "'";
        
        if($checksumReceived != null){
        	$isChecksumValid = $this -> verifyChecksum($checksumReceived, $walletConfig['secret_key'], $all);
        }

        if($isChecksumValid){
            // success
            if ($this->_validateResponse()) {
    			
                Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
    			$order2 = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
    			$order1 = Mage::getModel('sales/order')->load($postdata['orderid']);
    			$total_amount = (double)$order2->getGrandTotal();  //// total amount from database for an order ////			
    			//$_totalData = $order->getData();
    			//$_grand = $_totalData['grand_total'];  // another way to get grand total //
    			
    			error_log('Last Order id and amount is ' . $order2->getId() . ' & ' . $order2->getGrandTotal());
    			error_log('By POST Order id and amount is ' . $order1->getId() . ' & ' . $total_amount . ' & ' .  $session->getLastRealOrderId());
    			error_log('POSTED Order id and amount is ' . $postdata['orderid'] . ' & ' . $postdata['amount']);
    			
    			if( ((double)$postdata['amount'] == (double)$order2->getGrandTotal()) AND ($session->getLastRealOrderId() == $postdata['orderid'])) {
    
    				error_log('order macthed');
    			    // calculate checksum //
    				
    				$algo_wallet = 'sha256';
    				
                    $MerchantId =  $walletConfig['merchant_id'] ; // merchant ID //
    				$WorkingKey	=  $walletConfig['secret_key'] ; // merchant key ///			
    				$checksum_string_wallet = "'{$MerchantId}''{$postdata['orderid']}'";
    				$checksum_wallet = hash_hmac($algo_wallet, $checksum_string_wallet, $WorkingKey);   /// this is final checksum //
                    error_log('Initial checksum : ' . $checksum_wallet);
    				
    				
    				$url = "https://test.mobikwik.com/mobikwik/checkstatus";  
                    
                    $version = '2';
    				
					$fields = "mid=$MerchantId&orderid=".$postdata['orderid']."&checksum=$checksum_wallet&ver=2";
                  
    				// is cURL installed yet?
    				if (!function_exists('curl_init')){
    					
    					die('Sorry cURL is not installed!');
    				}
    				// then let's create a new cURL resource handle
    				$ch = curl_init();
    				 
    				// Now set some options (most are optional)
    				 
    				// Set URL to hit
    				curl_setopt($ch, CURLOPT_URL, $url);
    				 
    				// Include header in result? (0 = yes, 1 = no)
    				curl_setopt($ch, CURLOPT_HEADER, 0);
    				 
    				curl_setopt($ch, CURLOPT_POST, 1);
    				 
    				curl_setopt($ch, CURLOPT_POSTFIELDS,  $fields);
    				 
    				// Should cURL return or print out the data? (true = return, false = print)
    				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    				 
    				// Timeout in seconds
    				curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    
    
    				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    				 
    				// Download the given URL, and return output
    				error_log('getting outputXml');
    				$outputXml = curl_exec($ch);
    				error_log($outputXml);
    				// Close the cURL resource, and free system resources
    				curl_close($ch);    	
    				$outputXmlObject =  simplexml_load_string($outputXml);
                    
                    // Now calculate checksum for the response we have just received //
                    $checksum_string_checkapi = "'{$outputXmlObject->statuscode}''{$outputXmlObject->orderid}''{$outputXmlObject->refid}''{$outputXmlObject->amount}''{$outputXmlObject->statusmessage}''{$outputXmlObject->ordertype}'";    	
    				$checksum_check_api = hash_hmac($algo_wallet, $checksum_string_checkapi, $WorkingKey);
                    error_log('check status generated checksum is ' . $checksum_check_api);
                    error_log('Received checksum is ' . $outputXmlObject->checksum . 'amount received = ' . $outputXmlObject->amount . 'amount sent is ' . $postdata['amount']);
    				error_log('refid via post is ' . $postdata['refid'] . 'refid via server call is ' . $outputXmlObject->refid);
                    
                    //error_log('outputXmlObject->amount ' . (double)$outputXmlObject->amount . ' postdata->amount ' . (double)$postdata['amount']. ' postdata->amount '.(double)$postdata['amount']. ' total_amount '. (double)$total_amount);
    				
    				if(($checksum_check_api == $outputXmlObject->checksum) && ((double)$outputXmlObject->amount == (double)$postdata['amount']) && ((double)$postdata['amount'] == (double)$total_amount) ){	
    					error_log('entered in final step of response');
    					
    					//// All Save Part ///////////////////////////////////////////////////////////////////////////////
    				
    					// load the order and change the order status
    					$wallet = Mage::getModel('wallet/transact');
    					$state = $wallet->walletSuccessOrderState();

    					$this->set_order_status($postdata['orderid']);

    					$order = Mage::getModel('sales/order')->loadByIncrementId($postdata['orderid']);

					$order->setData('state', $state);
					$order->setStatus($state);
    					// also do something similar to capturing the payment here            
    					$payment = $order->getPayment();
    					$transaction = Mage::getModel('sales/order_payment_transaction');
    							$dummy_txn_id = 'MW_'.$postdata['orderId'];
    					$transaction->setOrderPaymentObject($payment)
    						->setTxnId($dummy_txn_id)
    						->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
    						->setIsClosed(0)
    						->save();
    					$order->save();            
    					$this->_redirect('checkout/onepage/success', array('_secure'=>true));
    				}
    				else {            
    					$this->showfailure("Verification failed");
    				}
    				
    			}
    			else {            
    				$this->showfailure("Order Mismatch");
    			}
            } 
    		else {            
    			$this->showfailure($postdata['statusmessage']);
            } 
        }else{
            $this->showfailure("This response is compromised.");
        }
    }

	 public function set_order_status($orderId) {
        $session = Mage::getSingleton('wallet/session');
        $session->setTransactStatus($orderId, 'SUCCESS');
    }
    
    //This function is added to calculate checksum in request.
	private function calculateChecksum($secretKey,$all) {    
				
		return $checksum;
	}
    
    /**
     * Method to verify the response checksum
     * @param String $receivedChecksum
     * @param String $secretKey
     * @param String $all
     */
	private function verifyChecksum($receivedChecksum,$secretKey,$all) {
	    $algo = 'sha256';
		$checksum =  hash_hmac($algo, $all, $secretKey);
        
        $isChecksumValid = False;
        
        if($checksum==$receivedChecksum){
            $isChecksumValid = True;
        }
		return $isChecksumValid;
	}

    /**
     * Verify the response coming into the server
     * @return boolean
     */
    protected function _validateResponse() 
    {
        $postdata = Mage::app()->getRequest()->getPost();
        $session = Mage::getSingleton('checkout/session');
        $flag = False;
        error_log('Response Code is ' . $postdata['statuscode']);
		
		if ((int)$postdata['statuscode'] == 0) {
			$flag = True;
		}
		else{			
			$flag = False;			
		}
        return $flag;
    }

    public function failureAction() 
    {
        $this->loadLayout();        
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }
}
