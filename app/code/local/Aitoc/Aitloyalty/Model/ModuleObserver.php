<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/ModuleObserver.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ ZSegBBZTWMZrRmUW('68d6f8bb35cdd306c9f0ec59b9915b3f'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_ModuleObserver
{
    public function __construct()
    {
    }
    
    public function onAitocModuleDisableBefore($observer)
    {
    	if ('Aitoc_Aitloyalty' == $observer->getAitocmodulename())
    	{
    		$oInstaller = $observer->getObject();
	        /* @var $oInstaller Aitoc_Aitsys_Model_Aitsys */
	        
    		$oDb     = Mage::getModel('sales_entity/order')->getReadConnection();
	        /* @var $oDb Varien_Db_Adapter_Pdo_Mysql */
	        $oSelect = $oDb->select();
	        /* @var $oSelect Varien_Db_Select */
    		
	        $oSelect->from(array('salesrule' => Mage::getSingleton('core/resource')->getTableName('salesrule')),
                          array(
                              'name'    => 'salesrule.name',
                              'rule_id' => 'salesrule.rule_id',
                          )
                      )
                    ->where('( (salesrule.conditions_serialized LIKE "%Aitoc_Aitloyalty%") OR (salesrule.simple_action LIKE "%surcharge%") )')
                    ->where('salesrule.is_active = "1"')
            ;
    		$aRules = $oDb->fetchAll($oSelect);
    		
    		if (count($aRules))
    		{
    			$oInstaller->addCustomError('Please disable or delete conditions provided with Loyalty Program extension from the following rules:');
    		}
    		foreach ($aRules as $aRule)
    		{
    			$oInstaller->addCustomError($aRule['name'] . ' (ID: ' . $aRule['rule_id'] . ')');
    		}
    	}
    }
    
} } 