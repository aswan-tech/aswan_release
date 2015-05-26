<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rewrite/FrontSalesQuoteAddressTotalDiscount.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ rSDejjrQVBrENgRV('e979e89f9599e9b937209393b31aff58'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Rewrite_FrontSalesQuoteAddressTotalDiscount extends Mage_Sales_Model_Quote_Address_Total_Discount
{
   
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount!=0) {
            if ($amount > 0)
            {
                $title = Mage::helper('sales')->__('Discount');
            } else 
            {
                $title = Mage::helper('sales')->__('Surcharge');
            }
            if ($code = $address->getCouponCode()) {
                if ($amount > 0)
                {
                    $title = Mage::helper('sales')->__('Discount (%s)', $code);
                } else 
                {
                    $title = Mage::helper('sales')->__('Surcharge (%s)', $code);
                }
            }
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>$title,
                'value'=>-$amount,
                'full_info' => $address->getFullDescr(),
            ));
        }
        return $this;
    }

} } 