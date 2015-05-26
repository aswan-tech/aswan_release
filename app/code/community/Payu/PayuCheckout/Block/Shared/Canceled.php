<?php


class Payu_PayuCheckout_Block_Shared_Canceled extends Mage_Core_Block_Template
{
    /**
     *  Return Error message
     *
     *  @return	  string
     */
    public function getErrorMessage ()
    {
        $msg = Mage::getSingleton('checkout/session')->getPayuCheckoutErrorMessage();
        Mage::getSingleton('checkout/session')->unsPayuCheckoutErrorMessage();
        return $msg;
    }

    /**
     * Get continue shopping url
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}