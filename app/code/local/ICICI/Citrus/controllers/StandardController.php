<?php

class ICICI_Citrus_StandardController extends Mage_Core_Controller_Front_Action {

    protected $_order;

    public function responseAction() {
	
	    require_once 'Zend/Crypt/Hmac.php';

		$txnid = "";
		$txnrefno = "";
		$txnstatus = "";
		$txnmsg = "";
		$firstName = "";
		$lastName = "";
		$email = "";
		$street1 = "";
		$city = "";
		$state = "";
		$country = "";
		$pincode = "";
		$mobileNo = "";
		$signature = "";
		$reqsignature = "";
		$data = "";
		$txnGateway = "";
		$paymentMode = "";
		$maskedCardNumber = "";
		$cardType = "";
		$flag = "dataTampered";

		
		
        $order = $this->getOrder();
		$orderAmount = round($order->getData('grand_total'), 2);
        $config = Mage::getSingleton('citrus/config');
        $secretKey = $config->getSecretKey();
        $session = Mage::getSingleton('checkout/session');
        
		
        if ($_POST) {
		
		        
				if(isset($_POST['TxId']))
				{
					$txnid = $_POST['TxId'];
					$data .= $txnid;
				}
				if(isset($_POST['TxStatus']))
				{
					$txnstatus = $_POST['TxStatus'];
					$data .= $txnstatus;
				}
				if(isset($_POST['amount']))
				{
					$amount = $_POST['amount'];
					$data .= $amount;
				}
				if(isset($_POST['pgTxnNo']))
				{
					$pgtxnno = $_POST['pgTxnNo'];
					$data .= $pgtxnno;
				}
				if(isset($_POST['issuerRefNo']))
				{
					$issuerrefno = $_POST['issuerRefNo'];
					$data .= $issuerrefno;
				}
				if(isset($_POST['authIdCode']))
				{
					$authidcode = $_POST['authIdCode'];
					$data .= $authidcode;
				}
				if(isset($_POST['firstName']))
				{
					$firstName = $_POST['firstName'];
					$data .= $firstName;
				}
				if(isset($_POST['lastName']))
				{
					$lastName = $_POST['lastName'];
					$data .= $lastName;
				}
				if(isset($_POST['pgRespCode']))
				{
					$pgrespcode = $_POST['pgRespCode'];
					$data .= $pgrespcode;
				}
				if(isset($_POST['addressZip']))
				{
					$pincode = $_POST['addressZip'];
					$data .= $pincode;
				}
				if(isset($_POST['signature']))
				{
					$signature = $_POST['signature'];
				}
				
		/*signature data end*/
		
		        $respSignature = $this->generateHmacKey($data,$secretKey);
				

				if(($signature != "" && strcmp($signature, $respSignature) != 0) || ($orderAmount != $amount))
				{
					$flag = "dataTampered";
				}
				else
				{
				  $flag = "dataValid";
				}

				
				if(strtoupper($txnstatus) != "SUCCESS")
				{
				  $flag = "dataTampered";
				}
				else
				{
				  $flag = "dataValid";
				}
				
           
        }
		

        $debug = Mage::getModel('citrus/api_debug')->load($TxnID, "transact_id");
        $debug->setResponseBody($data);
        $debug->save();

        if ($flag == "dataValid") {

            $_order = new Mage_Sales_Model_Order();

            $orderId = $session->getLastRealOrderId();

            $_order->loadByIncrementId($orderId);

            $_order->sendNewOrderEmail();
            try {
                $payment = $_order->getPayment();

                $payment->setTransactionId($TxnID)->capture(null);
				$_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
                $_order->setStatus('created');
                $_order->addStatusToHistory(
                        $_order->getStatus(),
                        Mage::helper('citrus')->__('Customer successfully returned from citrus')
                );
                $_order->save();
            } catch (Exception $e) {
                Mage::logException($e);
                //if we couldn't capture order, just leave it as NEW order.
            }
            $session->getQuote()->setIsActive(false)->save();
            $this->_redirect('checkout/onepage/successcitrus', array('_secure' => true));
        } else {
		   if(strtoupper($txnstatus) == "CANCELED")
		   {
		     $cartURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."checkout/cart/";
		     echo "<script language='javascript' type='text/javascript'>setTimeout('window.self.location=\'" . $cartURL . "\''); </script>";
		     exit;
		   }
		   else
		   {
             $this->_redirect('checkout/onepage/failure', array('_secure' => true));
		   }
        }
    }

    public function getOrder() {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    public function redirectAction() {

        $session = Mage::getSingleton('checkout/session');
        $session->setcitrusStandardQuoteId($session->getQuoteId());
        $config = Mage::getSingleton('citrus/config');
        $order = $this->getOrder();
        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $billingAdd = $order->getBillingAddress()->getData();

 
			require_once('citrus/lib/CitrusPay.php');
			require_once 'Zend/Crypt/Hmac.php';

            $secretKey = trim($config->getSecretKey());
			
		    $transactionNo = rand();
			$vanity = $config->getVanityUrl();
            $orderAmount = round($order->getData('grand_total'), 2);
			CitrusPay::setApiKey($secretKey,'production');

			
				$vanityUrl = $vanity;
				$currency = "INR";
				$data = "$vanityUrl$orderAmount$transactionNo$currency";
				$secSignature = $this->generateHmacKey($data,CitrusPay::getApiKey());
				$action = CitrusPay::getCPBase()."$vanityUrl";  
				$time = time()*1000;
				$time = number_format($time,0,'.','');
				
				$responseURL = $config->getResponseURL();
				
        
            $debug = Mage::getModel('citrus/api_debug');
            $debug->setData("transact_id", $transactionNo);
            $debug->setRequestBody(print_r($oMerchant, 1));
            $debug->save();
            $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('citrus')->__('Customer was redirected to citrus')
            );
            $order->save();
            //$this->redirect($url);
			
			
            ?>
			      <form action="<?php echo $action;?>" method="POST" name="TransactionForm" id="transactionForm">
					<input name="merchantTxnId" type="hidden" value="<?php echo $transactionNo;?>" />
					<input name="addressState" type="hidden" value="<?php echo $billingAdd['region'];?>" />
					<input name="addressCity" type="hidden" value="<?php echo $billingAdd['city'];?>" />
					<input name="addressStreet1" type="hidden" value="<?php echo $billingAdd['street'];?>" />
					<input name="addressCountry" type="hidden" value="India" />
					<input name="addressZip" type="hidden" value="<?php echo $billingAdd['postcode'];?>" />
					<input name="firstName" type="hidden" value="<?php echo $billingAdd['firstname'];?>" />
					<input name="lastName" type="hidden" value="<?php echo $billingAdd['lastname'];?>" />
					<input name="email" type="hidden" value="<?php echo $billingAdd['email'];?>" />
					<input name="paymentMode" type="hidden" value="NET_BANKING" />
					<input name="returnUrl" type="hidden" value="<?php echo $responseURL;?>" />
					<input name="orderAmount" type="hidden" value="<?php echo $orderAmount; ?>" />
					<input type="hidden" name="reqtime" value="<?php echo $time;?>" />
					<input type="hidden" name="secSignature" value="<?php echo $secSignature;?>" /> 
					<input type="hidden" name="currency" value="<?php echo $currency;?>" />
					<input name="phoneNumber" type="hidden" value="<?php echo $billingAdd['telephone'];?>" />
					<input name="issuerCode" type="hidden" value="" />
					<input name="cardHolderName" type="hidden" value="" />
					<input name="cardNumber" type="hidden" value="" />
					<input name="expiryMonth" type="hidden" value="" />
					<input name="cardType" type="hidden" value="" />
					<input name="cvvNumber" type="hidden" value="" />
					<input name="expiryYear" type="hidden" value="" />
                 </form>
                   <script language='javascript' type='text/javascript'>
				   document.getElementById("transactionForm").submit();
				   </script>";
	<?php
        
    }
	
	
		public	function generateHmacKey($data, $apiKey=null){
				$hmackey = Zend_Crypt_Hmac::compute($apiKey, "sha1", $data);
				return $hmackey;
			}

    public function cancelAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getcitrusStandardQuoteId());

        // cancel order
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }
        Mage::getSingleton('checkout/session')->addError("Thank you for shopping with us. However the transaction has been declined");
        $this->_redirect('checkout/cart');
    }

}
