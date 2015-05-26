<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Model/Aitindividpromo.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ fNkayWkampUNsEMh('68d530021f4685a4a730a25c777eeadd'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitindividpromo_Model_Aitindividpromo extends Mage_Eav_Model_Entity_Attribute
{
    public function getCustomerGroups($bToOption = true)
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value'=>0, 'label'=>Mage::helper('salesrule')->__('NOT LOGGED IN')));
        }
        
        if ($bToOption)
        {
            return $customerGroups;
        }
        else 
        {
            $aCustomerGroupHash = array();
            
            if ($customerGroups)
            {
                foreach ($customerGroups as $aGroup)
                {
                    $aCustomerGroupHash[$aGroup['value']] = $aGroup['label'];
                }
            }
            
            return $aCustomerGroupHash;
        }
    }
    
}
 } ?>