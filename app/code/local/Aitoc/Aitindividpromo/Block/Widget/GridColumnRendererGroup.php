<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Block/Widget/GridColumnRendererGroup.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ UIjBDajBZqqIsDaw('030de8d978734ec4a57843aa62a76b96'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Block_Widget_GridColumnRendererGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if (version_compare(Mage::getVersion(), '1.12.0.0', '>='))
        {
            $rule_id = $row->getData('rule_id');
            
            $collection = Mage::getModel('salesrule/rule')->load($rule_id);
            $collection
                ->getCollection()
                ->getSelect()
                ->join( array('table_alias'=>Mage::getSingleton('core/resource')->getTableName('salesrule_customer_group')), 'main_table.rule_id = table_alias.rule_id', array('table_alias.*'))
            ;

            $sGroups = implode(',', $collection->getData($this->getColumn()->getIndex()));
        }
        else
        {
            $sGroups = $row->getData($this->getColumn()->getIndex());
        }
        
        if ($sGroups OR $sGroups === '0')
        {
            $oAitindividpromo = Mage::getModel('aitindividpromo/aitindividpromo');
            
            $aCustomerGroupHash = $oAitindividpromo->getCustomerGroups(false);
            
            $aRowGroupHash = explode(',', $sGroups);
            
            $aRowGroupName = array();
            
            foreach ($aRowGroupHash as $iKey)
            {
                if (isset($aCustomerGroupHash[$iKey]))
                {
                    $aRowGroupName[] = $aCustomerGroupHash[$iKey];
                }
            }
            
            $sHtml = implode(', ', $aRowGroupName);
            
            return $sHtml;
        }
        else 
        {
            return '&nbsp;';
        }
    }

} } 