<?php
/**
 * @copyright   Copyright (c) 2009-11 Amasty
 */

class Amasty_Rules_Helper_Data extends Mage_Core_Helper_Abstract 
{
    const TYPE_CHEAPEST    = 'the_cheapest';
    const TYPE_EXPENCIVE   = 'the_most_expencive';
    const TYPE_FIXED       = 'fixed';
    const TYPE_EACH_N      = 'each_n';
    const TYPE_GROUP_N     = 'group_n';
    const TYPE_XY_PERCENT  = 'buy_x_get_y_percent';
    const TYPE_XY_FIXED    = 'buy_x_get_y_fixed';
    const TYPE_AFTER_N_FIXED     = 'after_n_fixed';
    const TYPE_AFTER_N_DISC      = 'after_n_disc';
    const TYPE_AMOUNT            = 'money_amount';     
        
    public function getDiscountTypes($asOptions=false)
    {
        $types = array(
            self::TYPE_CHEAPEST   => $this->__('The Cheapest'),
            self::TYPE_EXPENCIVE  => $this->__('The Most Expensive'),
            self::TYPE_FIXED      => $this->__('Each N-th for Fixed Price'),
            self::TYPE_EACH_N     => $this->__('Each N-th with Discount'),
            self::TYPE_GROUP_N    => $this->__('Each group of N for fixed price'),   
            self::TYPE_XY_PERCENT => $this->__('Buy X Get Y with Discount'),   
            self::TYPE_XY_FIXED   => $this->__('Buy X Get Y for Fixed Price'),   
            self::TYPE_AFTER_N_FIXED   => $this->__('All products after N for Fixed Price'),   
            self::TYPE_AFTER_N_DISC    => $this->__('All products after N with Discount'),   
            self::TYPE_AMOUNT          => $this->__('Get $Y for each $X spent'), 

        );
        
        if (!$asOptions){
            $values = array();
            foreach ($types as $k=>$v){
                $values[] = array(
                    'value' => $k, 
                    'label' => $v                
                );
            }
            $types = $values;
        }

        return $types;
    }
}