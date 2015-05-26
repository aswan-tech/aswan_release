<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rewrite/FrontSalesRuleRuleConditionCombine.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ DYBMggDUPmDaTyIP('f711d766f8f52210977cd038e4521ffb'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Rewrite_FrontSalesRuleRuleConditionCombine extends Mage_SalesRule_Model_Rule_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('salesrule/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array('value'=>'salesrule/rule_condition_address|'.$code, 'label'=>$label);
        }

//        $pAttributes = array(
//            array(
//                'value' => 'salesrule/rule_condition_product|custom_design_from',
//                'label' => 'Test Atrr',
//            ),
//        );
        
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' =>  'Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Subselect', 
                'label' =>  Mage::helper('aitloyalty')->__('Customer data subselection'),
            ),
        ));
        return $conditions;
    }
    
    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return true;
        }
        $bResult   = false;
        $bMeetCond = false;
        foreach ($this->getConditions() as $cond) {
            if ($cond instanceof Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Subselect)
            {
                $iStoreId = Mage::app()->getStore()->getId();
                $iSiteId  = Mage::app()->getWebsite()->getId();

                /* */
                $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitloyalty')->getLicense()->getPerformer();
                $ruler     = $performer->getRuler();
                if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
                {
                    return false;
                }
                /* */
                
                $bMeetCond = true;
                if ($this->getValue())
                {
                    // If ALL/ANY of these conditions are TRUE 
                    $bResult = $cond->validate($object) || false;
                } else 
                {
                    // If ALL/ANY of these conditions are FALSE 
                    $bResult = !($cond->validate($object) || false);
                }
            }
        }
        if ($bMeetCond)
        {
            if ('any' == $this->getAggregator())
            {
                return $bResult || parent::validate($object);
            } else 
            {
                return $bResult && parent::validate($object);
            }
        } else 
        {
            return parent::validate($object);
        }
    }
} } 