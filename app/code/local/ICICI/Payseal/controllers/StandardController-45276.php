<?php

class ICICI_Payseal_StandardController extends Mage_Core_Controller_Front_Action {

    protected $_order;

    public function SFAResponseAction() {

        include("Sfa/EncryptionUtil.php");
        $config = Mage::getSingleton('payseal/config');
        $merchantId = trim($config->getMerchantId());
        $keyPath = str_replace("/", "//", $config->getKeyPath()) . $merchantId . ".key";
        $strMerchantId = $merchantId;
        $astrFileName = $keyPath;
        $astrClearData;
        $ResponseCode = "";
        $Message = "";
        $TxnID = "";
        $ePGTxnID = "";
        $AuthIdCode = "";
        $RRN = "";
        $CVRespCode = "";
        $session = Mage::getSingleton('checkout/session');
        $session->setData("redirected", "false");
        if ($_POST) {
            if ($_POST['DATA'] == null) {
                print "null is the value";
            }
            $astrResponseData = $_POST['DATA'];
            $astrDigest = $_POST['EncryptedData'];
            $oEncryptionUtilenc = new EncryptionUtil();
            $astrsfaDigest = $oEncryptionUtilenc->getHMAC($astrResponseData, $astrFileName, $strMerchantId);

            if (strcasecmp($astrDigest, $astrsfaDigest) == 0) {
                parse_str($astrResponseData, $output);
                if (array_key_exists('RespCode', $output) == 1) {
                    $ResponseCode = $output['RespCode'];
                }
                if (array_key_exists('Message', $output) == 1) {
                    $Message = $output['Message'];
                }
                if (array_key_exists('TxnID', $output) == 1) {
                    $TxnID = $output['TxnID'];
                }
                if (array_key_exists('ePGTxnID', $output) == 1) {
                    $ePGTxnID = $output['ePGTxnID'];
                }
                if (array_key_exists('AuthIdCode', $output) == 1) {
                    $AuthIdCode = $output['AuthIdCode'];
                }
                if (array_key_exists('RRN', $output) == 1) {
                    $RRN = $output['RRN'];
                }
                if (array_key_exists('CVRespCode', $output) == 1) {
                    $CVRespCode = $output['CVRespCode'];
                }
            }
        }

        $debug = Mage::getModel('payseal/api_debug')->load($TxnID, "transact_id");
        $debug->setResponseBody(print_r($output, 1));
        $debug->save();

        if (($output['RespCode'] == 2 && $Message == "No Suitable Acquirer Found") || $output['RespCode'] == 0) {


            $_order = new Mage_Sales_Model_Order();

            $orderId = $session->getLastRealOrderId();

            $_order->loadByIncrementId($orderId);

            $_order->sendNewOrderEmail();
            try {
                $payment = $_order->getPayment();

                $payment->setTransactionId($TxnID)->capture(null);
                $_order->setStatus('created');
                $_order->addStatusToHistory(
                        $_order->getStatus(),
                        Mage::helper('payseal')->__('Customer successfully returned from payseal')
                );
                $_order->save();
            } catch (Exception $e) {
                Mage::logException($e);
                //if we couldn't capture order, just leave it as NEW order.
            }
            $session->getQuote()->setIsActive(false)->save();
            $this->_redirect('checkout/onepage/success', array('_secure' => true));
        } else {
            $this->_redirect('checkout/onepage/failure', array('_secure' => true));
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
		$redirected = $session->getData("redirected");
		if($redirected == "true")
		{
		  $session->setData("redirected", "false");
		  $cartURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."checkout/cart/";
		  echo "<script language='javascript' type='text/javascript'>setTimeout('window.self.location=\'" . $cartURL . "\''); </script>";
		  exit;
		}
        $session->setpaysealStandardQuoteId($session->getQuoteId());
        $config = Mage::getSingleton('payseal/config');
        $order = $this->getOrder();
        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        $shippingAdd = $order->getShippingAddress()->getData();
        $billingAdd = $order->getBillingAddress()->getData();

        include("Sfa/BillToAddress.php");
        include("Sfa/CardInfo.php");
        include("Sfa/Merchant.php");
        include("Sfa/MPIData.php");
        include("Sfa/ShipToAddress.php");
        include("Sfa/PGResponse.php");
        include("Sfa/PostLibPHP.php");
        include("Sfa/PGReserveData.php");

        $oMPI = new MPIData();
        $oCI = new CardInfo();
        $oPostLibphp = new PostLibPHP();
        $oMerchant = new Merchant();
        $oBTA = new BillToAddress();
        $oSTA = new ShipToAddress();
        $oPGResp = new PGResponse();
        $oPGReserveData = new PGReserveData();
        $merchantId = $config->getMerchantId();
        $responseURL = $config->getResponseURL();
        $transactionNo = "Transact" . rand();
        $oMerchant->setMerchantDetails($merchantId, $merchantId, $merchantId, "", $transactionNo, $order->getId(), $responseURL, "POST", "INR", "", "req.Sale", round($order->getData('grand_total'), 2), "", "", "true", "", "", "");
        $oBTA->setAddressDetails($billingAdd['customer_id'], $billingAdd['firstname'] . ' ' . $billingAdd['lastname'], $billingAdd['street'], "", "", $billingAdd['city'], $billingAdd['region'], $billingAdd['postcode'], "IND", $billingAdd['email']);
        $oSTA->setAddressDetails($shippingAdd['firstname'], $shippingAdd['lastname'], $shippingAdd['street'], $shippingAdd['city'], $shippingAdd['region'], $shippingAdd['postcode'], "IND", $shippingAdd['email']);

        $oPGResp = $oPostLibphp->postSSL($oBTA, $oSTA, $oMerchant, $oMPI, $oPGReserveData);

        if ($oPGResp->getRespCode() == "000") {
            $debug = Mage::getModel('payseal/api_debug');
            $debug->setData("transact_id", $transactionNo);
            $debug->setRequestBody(print_r($oMerchant, 1));
            $debug->save();
            $url = $oPGResp->getRedirectionUrl();
            $order->addStatusToHistory(
                    $order->getStatus(),
                    Mage::helper('payseal')->__('Customer was redirected to payseal')
            );
            $order->save();
            //$this->redirect($url);
			$session->setData("redirected", "true");
             echo "<html><head>
			        <title>Processing..</title></head><body><center><div><font size='5' color='#3b4455'>You are redirecting to patment gateway,<br/>Please wait ...<br/>
                   <font size='3'>(Please do not press 'Refresh' or 'Back' button)</font></font></div>
				   <img src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN)."frontend/enterprise/lecom/images/spinner.gif' />
				   </center>
				   </body></html>
                   <script language='javascript' type='text/javascript'>setTimeout('window.self.location=\'" . $url . "\'', 3000); </script>";
        } else {
            // Mage::logException($oPGResp->getRespMessage());
            Mage::getSingleton('core/session')->addError("Error Message:" . $oPGResp->getRespMessage() . " Please choose another payment method");
            $this->_redirect('onestepcheckout/?cart=true', array('_secure' => true));
        }
    }

    public function cancelAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getpaysealStandardQuoteId());

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
