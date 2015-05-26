<?php

class Magestore_Departmentimages_Model_System_Config_Styles 
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Yes')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('No')),
        );
    }
}