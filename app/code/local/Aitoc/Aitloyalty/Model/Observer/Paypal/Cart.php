<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Observer/Paypal/Cart.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ PYrZaaPAlDPOXjfl('2cb4d9eb329a454cea2a279bdcbe17ab'); ?><?php
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    Aitoc_Aitloyalty
 * @author lyskovets
 */
class Aitoc_Aitloyalty_Model_Observer_Paypal_Cart
    extends Aitoc_Aitloyalty_Model_Observer_Abstract
{
    private function _init($event)
    {
        $cart = $event->getPaypalCart();
        $this->setCart($cart);
    }
    
    public function process(Varien_Event_Observer $event)
    {
        $this->_init($event);
        $discount = $this->_getDiscount();
        if($discount > 0)
        {
            $this->_setSurcharge($discount);
            $this->_changeOriginalDiscount();
        }
        return; 
    }
    
    private function _getDiscount()
    {
        $salesEntity = $this->getCart()->getSalesEntity();
        if ($salesEntity instanceof Mage_Sales_Model_Order) 
        {
            $discount = $salesEntity->getBaseDiscountAmount();
        }                    
        else 
        {
            $address = $salesEntity->getIsVirtual() ?
                $salesEntity->getBillingAddress() : $salesEntity->getShippingAddress();
            $discount = $address->getBaseDiscountAmount(); 
        }
        return $discount;
    }
    
    private function _setSurcharge($value)
    {
        $this->getCart()->addItem(Mage::helper('paypal')->__('Surcharge'), 1, (float)$value,'surcharge');  
    }
    
    private function _changeOriginalDiscount()
    {
        $cart = $this->getCart();
        $name = $this->_getDiscountConstant();
        $value = -($this->_getDiscount());
        $cart->updateTotal($name, $value);
    }
    
    private function _getDiscountConstant()
    {
        return Mage_Paypal_Model_Cart::TOTAL_DISCOUNT;
    }
} } 