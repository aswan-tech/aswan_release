<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rule/Condition/Customer/Combine.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ aYjBeeaROgakQMTO('fb8826776aa413d3189d1e47fc8c7d9d'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Combine');
    }

    public function isModuleEnabled($moduleName='')
    {        
        $aModuleList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();
        
        $data = array(
            'is_installed' => false,
            'is_enabled' => false,
        );
        
        $aModuleList = Mage::getModel('aitsys/aitsys')->getAitocModuleList();
        
        if ($aModuleList)
        {
            foreach ($aModuleList as $module)
            {                
                if ($moduleName == $module['key'])
                {
                    $data = array(
                        'is_installed' => (bool) $module->isAvailable(),
                        'is_enabled' => (bool) $module['value'],
                    );
                }
            }
        }

        return new Varien_Object($data);
    }
   
        
    public function loadArray($arr, $key='conditions')
    {
        $module = $this->isModuleEnabled('Aitoc_Aitloyalty');
        if (!$module->getIsEnabled()) {
            return $this;
        }
        parent::loadArray($arr, $key);
        return $this;
    }
    public function getNewChildSelectOptions()
    {
    	$cAttributes[] = array('value' => 'Aitoc_Aitloyalty_Model_Rule_Condition_Customer|amount_during_period', 'label' => 'Amount spent during period');
    	$cAttributes[] = array('value' => 'Aitoc_Aitloyalty_Model_Rule_Condition_Customer|membership_period',    'label' => 'Period of membership');
    	$cAttributes[] = array('value' => 'Aitoc_Aitloyalty_Model_Rule_Condition_Customer|amount_average',       'label' => 'Average order amount during period');

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Combine', 'label'=>Mage::helper('catalog')->__('Conditions Combination')),
            array('label'=>Mage::helper('catalog')->__('Customer Data'), 'value'=>$cAttributes),
        ));
        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }

    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all    = $this->getAggregator() === 'all';
        $true   = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            $validated = $cond->validate($object);

            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }
    
} } 