<?php
class ICICI_citrus_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'citrus_standard';
    protected $_formBlockType = 'citrus/standard_form';
    protected $_canUseInternal = false;
    
    public function getConfig()
    {
        return Mage::getSingleton('citrus/config');
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
        $block = $this->getLayout()->createBlock('citrus/form_standard', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

   
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('citrus/standard/redirect');
    }

}


