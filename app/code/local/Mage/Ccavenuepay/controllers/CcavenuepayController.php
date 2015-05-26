<?php
class Mage_Ccavenuepay_CcavenuepayController extends Mage_Core_Controller_Front_Action {

    protected $_order;

    public function getOrder() {
        if ($this->_order == null) {

        }
        return $this->_order;
    }

    protected function _expireAjax() {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    public function getStandard() {
        return Mage::getSingleton('Ccavenuepay/standard');
    }

    public function redirectAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setCcavenuepayStandardQuoteId($session->getQuoteId());
        $order = Mage::getModel('sales/order');
        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        $order->save();

        $this->getResponse()->setBody($this->getLayout()->createBlock('Ccavenuepay/form_redirect')->toHtml());
        $session->unsQuoteId();
    }

    public function cancelAction() {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getCcavenuepayStandardQuoteId(true));
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->addStatusHistoryComment("Returned from CC with failure");
                $order->cancel()->save();
            }
        }
        Mage::getSingleton('checkout/session')->addError("Thank you for shopping with us. However, the transaction has been declined.");
        $this->_redirect('checkout/onepage/successCcavenue');
    }

    public function successAction() {
        $response = $this->getRequest()->getPost();
        if(isset($response["Merchant_Id"])) $Merchant_Id=$response["Merchant_Id"];
        if(isset($response["Amount"])) $Amount= $response["Amount"];
        if(isset($response["Order_Id"])) $Order_Id=$response["Order_Id"];
        if(isset($response["Merchant_Param"])) $Merchant_Param= $response["Merchant_Param"];
        if(isset($response["Checksum"])) $Checksum= $response["Checksum"];
        if(isset($response["AuthDesc"])) $AuthDesc=$response["AuthDesc"];

        $WorkingKey = Mage::getStoreConfig('payment/ccavenuepay/workingkey');
        if(empty($WorkingKey)){
            $WorkingKey = "ylpuns4t5luo5gluok";
        }
        $ccavenuepay = Mage::getModel('ccavenuepay/method_ccavenuepay');

        $Checksum = $ccavenuepay->verifyChecksum($Merchant_Id, $Order_Id, $Amount, $AuthDesc, $Checksum, $WorkingKey);
        if ($Checksum == "true" && $AuthDesc == "N") {
            $this->getCheckout()->setCcavenuepayErrorMessage('CCAVENUE UNSUCCESS');
            $this->cancelAction();
            return false;
        } else if ($Checksum == "true" && $AuthDesc == "B") {
            $this->getCheckout()->setCcavenuepayErrorMessage('CCAVENUE UNSUCCESS');
            $this->cancelAction();
            return false;
        } else if ($Checksum == "false") {
            $this->getCheckout()->setCcavenuepayErrorMessage('CCAVENUE UNSUCCESS');
            $this->cancelAction();
            return false;
        }
        $session = $this->getCheckout();
        $session->setQuoteId($session->getCcavenuepayStandardQuoteId());
        $session->unsCcavenuepayStandardQuoteId();

        $order = Mage::getModel('sales/order')->load( Mage::getSingleton('checkout/session')->getLastOrderId() );
        $f_passed_status = Mage::getStoreConfig('payment/ccavenuepay/payment_success_status');
        $message = Mage::helper('Ccavenuepay')->__('Your payment is authorized.');
        $order->addStatusToHistory( $f_passed_status, $message );
        
        $payment_confirmation_mail = Mage::getStoreConfig('payment/ccavenuepay/payment_confirmation_mail');
        if ($payment_confirmation_mail == "1") {
            $order->sendNewOrderEmail();
        }
        $order->save();
        foreach( $session->getQuote()->getItemsCollection() as $item ){
            Mage::getSingleton('checkout/cart')->removeItem( $item->getId() )->save();
        }


        $redirect_url = Mage::getUrl('checkout/onepage/successCcavenue');
        Mage::app()->getFrontController()->getResponse()->setRedirect($redirect_url);
    }

    public function errorAction() {
        $this->_redirect('checkout/onepage/');
    }

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }
}
