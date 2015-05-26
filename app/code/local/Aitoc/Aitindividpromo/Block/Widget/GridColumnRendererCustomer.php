<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Block/Widget/GridColumnRendererCustomer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ RTZjakZjrwCTsako('61f71cfbf2e078534bd1769ec4a25941'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Block_Widget_GridColumnRendererCustomer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $aRuleCustomerHash = Mage::registry('aitindividpromo_data');

        $iRuleId = $row->getData('rule_id');

        if ($iRuleId AND $aRuleCustomerHash AND isset($aRuleCustomerHash[$iRuleId]) AND $aRuleCustomerHash[$iRuleId])
        {
            $iRecordsLimit = 3;
            
            $bOverLimit = false;
            
            if (sizeof($aRuleCustomerHash[$iRuleId]) > $iRecordsLimit)
            {
                $aCustomerHash = array_slice($aRuleCustomerHash[$iRuleId], 0, $iRecordsLimit);
                $bOverLimit = true;
            }
            else 
            {
                $aCustomerHash = $aRuleCustomerHash[$iRuleId];
            }
            
            $sHtml = implode('<br>', $aCustomerHash);
            
            if ($bOverLimit)
            {
                $sHtml .= '<br>...';
            }
            
            return $sHtml;
            
        }
        else 
        {
            return '&nbsp;';
        }
    }

} } 