<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Rewrite/Front/Sales/Order/Totals.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ BSmkyyBCrrBjcahr('210c8dccb5bd3a467cc640d51f9e3483'); ?><?php
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    Aitoc_Aitloyalty
 * @author lyskovets
 */
class Aitoc_Aitloyalty_Block_Rewrite_Front_Sales_Order_Totals 
    extends Mage_Sales_Block_Order_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        $source = $this->getSource();

        $this->_totals = array();
        $this->_totals['subtotal'] = new Varien_Object(array(
            'code'  => 'subtotal',
            'value' => $source->getSubtotal(),
            'label' => $this->__('Subtotal')
        ));


        /**
         * Add shipping
         */
        if (!$source->getIsVirtual() && ((float) $source->getShippingAmount() || $source->getShippingDescription()))
        {
            $this->_totals['shipping'] = new Varien_Object(array(
                'code'  => 'shipping',
                'field' => 'shipping_amount',
                'value' => $this->getSource()->getShippingAmount(),
                'label' => $this->__('Shipping & Handling')
            ));
        }

        /**
         * Add discount
         */
        $amount = (float)$this->getSource()->getDiscountAmount();
        $part = Mage::helper('aitloyalty/discount')->getTitlePart($amount); 
        if ($amount != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = $this->__($part.' (%s)', $source->getDiscountDescription());
            } else {
                $discountLabel = $this->__($part);
            }
	/*Add coupon code start by*/
	$_order   = $this->getOrder();			
	if ($_order->getCouponCode() && $discountLabel == 'Discount'){			
		$discountLabel = $this->__('Discount').'( '.$_order->getCouponCode().' )';
	}
	/*Add coupon code end by*/
            $this->_totals['discount'] = new Varien_Object(array(
                'code'  => 'discount',
                'field' => 'discount_amount',
                'value' => $source->getDiscountAmount(),
                'label' => $discountLabel
            ));
        }

        $this->_totals['grand_total'] = new Varien_Object(array(
            'code'  => 'grand_total',
            'field'  => 'grand_total',
            'strong'=> true,
            'value' => $source->getGrandTotal(),
            'label' => $this->__('Grand Total')
        ));

        /**
         * Base grandtotal
         */
        if ($this->getOrder()->isCurrencyDifferent()) {
            $this->_totals['base_grandtotal'] = new Varien_Object(array(
                'code'  => 'base_grandtotal',
                'value' => $this->getOrder()->formatBasePrice($source->getBaseGrandTotal()),
                'label' => $this->__('Grand Total to be Charged'),
                'is_formated' => true,
            ));
        }
        return $this;
    }
} } 