<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Rewrite/Front/SalesRule/Quote/Discount.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ PYrZaaPAlDPOXjfl('5ced465144c9d12024671a9ac0b45d03'); ?><?php
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    Aitoc_Aitloyalty
 * @author lyskovets
 */
class Aitoc_Aitloyalty_Model_Rewrite_Front_SalesRule_Quote_Discount
    extends Mage_SalesRule_Model_Quote_Discount
{
     /**
     * Add discount/surcharge total information to address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Discount
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getDiscountAmount();
        $part = Mage::helper('aitloyalty/discount')->getTitlePart($amount);
        if ($amount!=0) {
            $description = $address->getDiscountDescription();
            if ($description) {
                $title = Mage::helper('sales')->__($part.' (%s)', $description);
            } else {
                $title = Mage::helper('sales')->__($part);
            }
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $amount,
                'full_info' => $address->getFullDescr(),
            ));
        }
        return $this;
    }

} } 