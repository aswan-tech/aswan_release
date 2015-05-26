<?php
class Amasty_Birth_Model_Source_Discount extends Varien_Object
{
    public function toOptionArray()
    {
        $vals = array(
            'by_percent' => Mage::helper('salesrule')->__('Percent of product price discount'),
            'by_fixed'   => Mage::helper('salesrule')->__('Fixed amount discount'),
            'cart_fixed' => Mage::helper('salesrule')->__('Fixed amount discount for whole cart'),
        );
        
    	if (Mage::helper('ambase')->isModuleActive('Amasty_Promo')){
            $vals['ampromo_cart'] = Mage::helper('ampromo')->__('Auto add promo items for the whole cart');
    	}        

        $options = array();
        foreach ($vals as $k => $v){
            $options[] = array(
                    'value' => $k,
                    'label' => $v
            );
        }
        
        return $options;
    }
}