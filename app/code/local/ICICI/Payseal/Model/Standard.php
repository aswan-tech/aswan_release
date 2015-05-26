<?php
class ICICI_payseal_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'payseal_standard';
    protected $_formBlockType = 'payseal/standard_form';
    protected $_canUseInternal = false;
    
    public function getConfig()
    {
        return Mage::getSingleton('payseal/config');
    }

    public function validate()
    {
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
      
        return $this;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payseal/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

   
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('payseal/standard/redirect');
    }

}


