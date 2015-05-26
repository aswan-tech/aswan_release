<?php
class Amasty_Birth_Model_Source_Group extends Varien_Object
{
    public function toOptionArray()
    {
        $groups = Mage::getResourceModel('customer/group_collection')
            ->load()
            ->toOptionHash(); 
        
        $options = array();
        foreach ($groups as $k => $v){
            $options[] = array(
                    'value' => $k,
                    'label' => $v
            );
        }
        return $options;
    }
}