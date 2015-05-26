<?php

abstract class Payu_PayuCheckout_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Redirect Block
     * need to be redeclared
     */
    protected $_redirectBlockType;

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * when customer select 2CO payment method
     */
    public function redirectAction()
    {
               
        
        $session = $this->getCheckout();
        $quoteId = $session->getQuoteId();
        if(!empty($quoteId)) {
            $session->setPayuCheckoutQuoteId($quoteId);
            $session->setPayuCheckoutRealOrderId($session->getLastRealOrderId());

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            $order->addStatusToHistory($order->getStatus(), Mage::helper('payucheckout')->__('Customer was redirected to payu.'));
            $order->save();

            $this->getResponse()->setBody(
                $this->getLayout()
                    ->createBlock($this->_redirectBlockType)
                    ->setOrder($order)
                    ->toHtml()
            );        
            $session->unsQuoteId();
            $session->unsLastRealOrderId();
        } else {
            $this->_redirect('checkout/cart');
            return;
        }
    }


   

}