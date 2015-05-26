<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Rewrite/Adminhtml/Sales/Totals.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ PYrZaaPAlDPOXjfl('4ef9a9608312c792a8eb64feb452b084'); ?><?php
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    Aitoc_Aitloyalty
 * @author lyskovets
 */
class Aitoc_Aitloyalty_Block_Rewrite_Adminhtml_Sales_Totals
    extends Mage_Adminhtml_Block_Sales_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        $this->_totals = array();
        $this->_totals['subtotal'] = new Varien_Object(array(
            'code'      => 'subtotal',
            'value'     => $this->getSource()->getSubtotal(),
            'base_value'=> $this->getSource()->getBaseSubtotal(),
            'label'     => $this->helper('sales')->__('Subtotal')
        ));

        /**
         * Add shipping
         */
        if (!$this->getSource()->getIsVirtual() && ((float) $this->getSource()->getShippingAmount() || $this->getSource()->getShippingDescription()))
        {
            $this->_totals['shipping'] = new Varien_Object(array(
                'code'      => 'shipping',
                'value'     => $this->getSource()->getShippingAmount(),
                'base_value'=> $this->getSource()->getBaseShippingAmount(),
                'label' => $this->helper('sales')->__('Shipping & Handling')
            ));
        }

        /**
         * Add discount
         */
        $amount = (float)$this->getSource()->getDiscountAmount();
        $part = Mage::helper('aitloyalty/discount')->getTitlePart($amount);
        if ($amount != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = $this->helper('sales')->__($part.' (%s)', $this->getSource()->getDiscountDescription());
            } else {
                $discountLabel = $this->helper('sales')->__($part);
            }
            $this->_totals['discount'] = new Varien_Object(array(
                'code'      => 'discount',
                'value'     => $this->getSource()->getDiscountAmount(),
                'base_value'=> $this->getSource()->getBaseDiscountAmount(),
                'label'     => $discountLabel
            ));
        }

        $this->_totals['grand_total'] = new Varien_Object(array(
            'code'      => 'grand_total',
            'strong'    => true,
            'value'     => $this->getSource()->getGrandTotal(),
            'base_value'=> $this->getSource()->getBaseGrandTotal(),
            'label'     => $this->helper('sales')->__('Grand Total'),
            'area'      => 'footer'
        ));

        return $this;
    }
     
} } 