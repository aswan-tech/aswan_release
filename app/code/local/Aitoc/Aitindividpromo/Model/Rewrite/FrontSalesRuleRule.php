<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Model/Rewrite/FrontSalesRuleRule.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ cCBMeDBMjooCseDq('2d7d728c2a2634bd8a8f08cf7ac0df69'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */


class Aitoc_Aitindividpromo_Model_Rewrite_FrontSalesRuleRule extends Mage_SalesRule_Model_Rule
{
    protected function _afterSave()
    {
        $iRuleId = $this->getId();
        
        $oReq = Mage::app()->getFrontController()->getRequest();
        
        $aData = $oReq->getPost();
		/* This check has been added for Amastsy extension to work properly while sending coupons through cron */
		if(empty($aData)){
			return parent::_afterSave();
		}
		/* This check has been added for Amastsy extension to work properly while sending coupons through cron */
		
		/* modifying this line to trigger newsletter subscription coupon */
			if (!isset($aData['customer_individ_ids'])){
				return parent::_afterSave();
			}
		/* modifying this line to trigger newsletter subscription coupon */
        
		$oResource = Mage::getSingleton('core/resource');
		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        
        
        $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
            
        if (!isset($aData['customer_group_ids']) OR !$aData['customer_group_ids'])
        {
            if (version_compare(Mage::getVersion(), '1.12.0.0', '>='))
            {
                $oDb->delete(Mage::getSingleton('core/resource')->getTableName('salesrule_customer_group'), 'rule_id = ' . $iRuleId); 
            }
            else
            {
                $oResModel = Mage::getModel('salesrule/mysql4_rule');
                
                $oDb->update($oResModel->getMainTable(), array('customer_group_ids' => ''), 'rule_id = ' . $iRuleId); 
            }
        }
        
        
        if ($aData['customer_individ_ids'])
        {
            $aCustomerHash = explode('_', $aData['customer_individ_ids']);

#            $oDb->update($oResModel->getMainTable(), array('customer_group_ids' => 'individ'), 'rule_id = ' . $iRuleId); 
        }
        else 
        {
            $aCustomerHash = array();
            
#            $oDb->update($oResModel->getMainTable(), array('customer_group_ids' => implode(',', $aData['customer_group_ids'])), 'rule_id = ' . $iRuleId);
        }
         
        
        $oDb->delete($sTable, 'entity_id = ' . $iRuleId);
        
        if ($aCustomerHash)
        {
            foreach ($aCustomerHash as $sKey => $sValue)
            {
                if ($sValue)
                {
                    $aDBInfo = array
                    (
                        'entity_id'     => $iRuleId,
                        'customer_id'  => $sValue,
						'coupon_code'  => $aData['coupon_code']
                    );
                    $oDb->insert($sTable, $aDBInfo);
                }
            }
        }
        
        return parent::_afterSave();
    }
} } 