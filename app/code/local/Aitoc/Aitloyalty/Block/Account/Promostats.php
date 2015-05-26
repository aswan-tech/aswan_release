<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Account/Promostats.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ ZSegBBZTWMZrRmUW('3139d183afd7863c201c3ba12dfefbd9'); ?><?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitloyalty_Block_Account_Promostats extends Mage_Core_Block_Template
{
 private $_couponCodesUses;
    
    public function getCouponCodes($rule)
    {
        $couponCode = null;
        $aCouponCode = array();
       
        if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
        {
       
            $couponCode = $rule->getCode();
            if (!$couponCode)
            {
                $couponCodes = Mage::getResourceModel('salesrule/coupon_collection');
                $couponCodes->addRuleToFilter($rule);
                $couponCodes->getSelect()->limit(500);
                foreach($couponCodes as $_code)
                {
                    if ($this->_isCodeAvailible($rule,$_code))
                    {
                        $aCouponCode[] = $_code->getCode();
                    }
                }
                $couponCode = join(', ',$aCouponCode);
            }
            return $couponCode;
        }
        else
        {
            return $rule->getCouponCode()?$rule->getCouponCode():null;
        }
    }
    
    public function getCodeUsageArray()
    {
        /*$couponCodesUses = Mage::getResourceModel('salesrule/coupon_usage_collection');
        $read = $this->getSelect();
        $select = $read->from($couponCodesUses->getMainTable());
        $data = $read->fetchRow($select);
      */
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $query = 'SELECT * FROM ' . Mage::getSingleton('core/resource')->getTableName('salesrule_coupon_usage');
        $data = $read->fetchAll($query);

        $couponCodesUses = array();
        foreach ($data as $usageItems)
        {
            $couponCodesUses[$usageItems['coupon_id']][$usageItems['customer_id']] = $usageItems['times_used'];
        }
        return $this->_couponCodesUses = $couponCodesUses;
    }
    
    private function _isCodeAvailible($rule,$code)
    {
        if ($code->getData('usage_limit') && ($code->getData('usage_limit') <= $code->getData('times_used')))
        {
            return false;
        }
        if ($rule->getUsesPerCustomer() && ($code->getData('usage_per_customer') <= $this->_couponCodesUses[$code->getCode()][Mage::getSingleton('customer/session')->getCustomerId()]))
        {
            return false;
        }
        if($code->getData('expiration_date') && ($code->getData('expiration_date') <= Mage::getModel('core/date')->timestamp(time())))
        {
            return false;
        }
        return true;
    }
    public function getLoyaltyRules()
    {
		$oDb     = Mage::getModel('sales_entity/order')->getReadConnection();
		
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        
        $iStoreId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore($iStoreId)->getWebsiteId();
        
        $stores = Mage::getModel('core/store')
            ->getResourceCollection()
            ->setLoadDefault(true)
            ->load();
        
        foreach ($stores as $store) {
            if ($store->getId() != 0) 
            {
                $iDefaultStoreId = $store->getId();
                break;
            }
        }

        $rules = Mage::getResourceModel('salesrule/rule_collection');

        $now = Mage::getModel('core/date')->date('Y-m-d');

        $rules->getSelect()->where('is_active=1');
        if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
        {
            $rules->getSelect()->joinLeft(array('salesrule_website'=>Mage::getSingleton('core/resource')->getTableName('salesrule_website')),'`main_table`.`rule_id`=`salesrule_website`.`rule_id`');
            $rules->getSelect()->where('salesrule_website.website_id = ?',(int)$websiteId);
        }
        else
        {
            $rules->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);
        }
        
        // fix for invidual promotions module
        
        $extensionName = 'Aitoc_Aitindividpromo';        
        $oIsExtensionActive = Mage::getConfig()->getNode('modules/' . $extensionName . '/active');
        if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
        {        
            $rules->getSelect()->joinLeft(array('salesrule_customer_group'=>Mage::getSingleton('core/resource')->getTableName('salesrule_customer_group')),'`main_table`.`rule_id`=`salesrule_customer_group`.`rule_id`');
        }
        
        if ((string)$oIsExtensionActive == 'true')
        {
    		$oResource = Mage::getSingleton('core/resource');
    		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        
            
            if ($sTable)
            {
                if (!$customerId) 
                {
                    $customerId = 0;
                    
                    // fix for create order by admin
                    
                    if ($_SESSION AND isset($_SESSION['adminhtml_quote']) AND isset($_SESSION['adminhtml_quote']['customer_id']) AND $_SESSION['adminhtml_quote']['customer_id'])
                    {
                        $customerId = $_SESSION['adminhtml_quote']['customer_id'];
                    }
                }
                
                $rules->getSelect()->joinLeft(array('rc' => $sTable), 'main_table.rule_id = rc.entity_id AND rc.customer_id = ' . $customerId);
                
                if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
                {
                    $sWhere = '(rc.entity_id IS NOT NULL) OR (salesrule_customer_group.customer_group_id=' . (int)$customerGroupId . ')';
                }
                else
                {
                    $sWhere = '(rc.entity_id IS NOT NULL) OR find_in_set('.(int)$customerGroupId.',customer_group_ids)';
                }                
        
                $rules->getSelect()->where($sWhere);
               
            }
        }
        else 
        {
            if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
            {
                $rules->getSelect()->where('salesrule_customer_group.customer_group_id=?', (int)$customerGroupId);
            }
            else 
            {
                $rules->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);
            }
        }
        
        $rules->getSelect()->where('from_date is null or from_date<=?', $now);
        $rules->getSelect()->where('to_date is null or to_date>=?', $now);
	    $rules->getSelect()->order('sort_order');
	    
        $rules->getSelect()->joinInner(array('display' => Mage::getSingleton('core/resource')->getTableName('aitoc_salesrule_display')), 'main_table.rule_id = display.rule_id', array('coupone_enable'));
                
        $rules->getSelect()->joinInner(array('d_title' => Mage::getSingleton('core/resource')->getTableName('aitoc_salesrule_display_title')), 'main_table.rule_id = d_title.rule_id AND store_id = ' . $iDefaultStoreId, array('value'));
        //print '========'.$rules->getSelect();//die;
        $rules->load();

        if ($iStoreId != $iDefaultStoreId)
        {
            foreach ($rules as $rule) 
            {
                $oSelect = $oDb->select();
            	
                $oSelect->from(array('salesrule' => Mage::getSingleton('core/resource')->getTableName('aitoc_salesrule_display_title')), array('value'))
                        ->where('salesrule.rule_id = "' . $rule->getRuleId() . '" AND store_id = ' . $iStoreId)
                ;
            	
                $sValue = $oDb->fetchOne($oSelect);

                if ($sValue)
                {
                    $rule->setValue($sValue);
                }
                
            }
        }

        foreach ($rules as $rule) 
        {
            if ($rule->getIsValid() === false) {
                continue;
            }

            if ($rule->getIsValid() !== true) {
                /**
                 * too many times used in general
                 */
                if ($rule->getUsesPerCoupon() && ($rule->getTimesUsed() >= $rule->getUsesPerCoupon())) {
                    $rule->setIsValid(false);
                    continue;
                }
                /**
                 * too many times used for this customer
                 */
                $ruleId = $rule->getId();
                if ($ruleId && $rule->getUsesPerCustomer()) {
                    
                    $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                    
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
                    if ($ruleCustomer->getId()) {
                        if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                            continue;
                        }
                    }
                }
                $rule->afterLoad();
                
                /**
                 * passed all validations, remember to be valid
                 */
                $rule->setIsValid(true);
            }
            
            $discountAmount = 0;
            $baseDiscountAmount = 0;
            switch ($rule->getSimpleAction()) {
                case 'to_percent':

                case 'by_percent':
                    break;

                    /* AITOC modifications */
                case 'by_percent_surcharge':
                    
                    $rule->setIsSurcharge(true);
                	break;
                	
                case 'by_fixed_surcharge':
                    $rule->setIsSurcharge(true);
                	break;
                case 'cart_fixed_surcharge':
                    $rule->setIsSurcharge(true);
                    break;
                	/* AITOC modifications end */
                    
                case 'to_fixed':
                    break;

                case 'by_fixed':
                    break;

                case 'cart_fixed':
                    break;

                case 'buy_x_get_y':
                    break;
            }
            
            if ($rule->getCouponCode() && ( strtolower($rule->getCouponCode()) == strtolower($this->getCouponCode()))) {
                $address->setCouponCode($this->getCouponCode());
            }
            $rule->setIsLoyaltyValid($this->validate($rule));
                
            if (strpos($rule->getSimpleAction(), 'percent'))
            {
                $nAmount = $rule->getDiscountAmount();
                
                if (round($nAmount) == $nAmount)
                {
                    $rule->setDiscountAmount(round($nAmount));
                }
                else 
                {
                    $rule->setDiscountAmount(rtrim($nAmount, '0'));
                }
            }
        }
        if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
        {
            $this->getCodeUsageArray();
        }
        return $rules;
    }
    
	
	public function getLoyaltyRulesCustom()
    {
		$oDb     = Mage::getModel('sales_entity/order')->getReadConnection();
		
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        
        $iStoreId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore($iStoreId)->getWebsiteId();
        
        $stores = Mage::getModel('core/store')
            ->getResourceCollection()
            ->setLoadDefault(true)
            ->load();
        
        foreach ($stores as $store) {
            if ($store->getId() != 0) 
            {
                $iDefaultStoreId = $store->getId();
                break;
            }
        }

        $rules = Mage::getResourceModel('salesrule/rule_collection')
				->addFieldToSelect(array('name', 'description', 'from_date', 'to_date', 'uses_per_customer', 'is_active', 'simple_action', 'discount_amount', 'coupon_type', 'use_auto_generation', 'times_used', 'uses_per_coupon'));
		
		
        $now = Mage::getModel('core/date')->date('Y-m-d');
		
        $rules->getSelect()->where('is_active=1');
		
        if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
        { $rules->getSelect()->joinLeft(
									array('salesrule_website'=>Mage::getSingleton('core/resource')->getTableName('salesrule_website')),
									'`main_table`.`rule_id`=`salesrule_website`.`rule_id`',
									array('website_id')
								);
            $rules->getSelect()->where('salesrule_website.website_id = ?',(int)$websiteId);
        }
        else
        {
            $rules->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);
        }
        // fix for invidual promotions module
        
        $extensionName = 'Aitoc_Aitindividpromo';        
        $oIsExtensionActive = Mage::getConfig()->getNode('modules/' . $extensionName . '/active');
        if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
        {        
            $rules->getSelect()->joinLeft(array('salesrule_customer_group'=>Mage::getSingleton('core/resource')->getTableName('salesrule_customer_group')),'`main_table`.`rule_id`=`salesrule_customer_group`.`rule_id`',array('customer_group_id'));
        }
		
		//extrension code run even when it is disabled
        if (true)//(string)$oIsExtensionActive == 'true')
        {
    		$oResource = Mage::getSingleton('core/resource');
    		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');       
            if ($sTable)
            {
                if (!$customerId) 
                {
                    $customerId = 0;
                    
                    // fix for create by admin
                    if ($_SESSION AND isset($_SESSION['adminhtml_quote']) AND isset($_SESSION['adminhtml_quote']['customer_id']) AND $_SESSION['adminhtml_quote']['customer_id'])
                    {
                        $customerId = $_SESSION['adminhtml_quote']['customer_id'];
                    }
                }
                
				$rules->getSelect()->joinLeft(array('rc' => $sTable), 'main_table.rule_id = rc.entity_id AND rc.customer_id = ' . $customerId . ' AND (rc.coupon_code = rule_coupons.code)', array('customer_id', 'coupon_code'));//Updated by Vishal
				
				//where added to Filter promotion rules that are only required for this specific customer
				$rules->getSelect()->where('rc.customer_id = '.(int)$customerId);
				   
				
                if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
                {
                    $sWhere = '(rc.entity_id IS NOT NULL) OR (salesrule_customer_group.customer_group_id=' . (int)$customerGroupId . ')';
                }
                else
                {
                    $sWhere = '(rc.entity_id IS NOT NULL) OR find_in_set('.(int)$customerGroupId.',customer_group_ids)';
                }                
        
                $rules->getSelect()->where($sWhere);
				
            }
        }
        else 
        {
            if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
            {
                $rules->getSelect()->where('salesrule_customer_group.customer_group_id=?', (int)$customerGroupId);
            }
            else 
            {
                $rules->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);
            }
        }
					
		
        $rules->getSelect()->where('from_date is null or from_date<=?', $now);
        $rules->getSelect()->where('to_date is null or to_date>=?', $now);
		
		$rules->getSelect()->where('main_table.uses_per_coupon = 0 OR (main_table.uses_per_coupon > 0 AND (main_table.uses_per_coupon > rule_coupons.times_used))');
		
		$rules->getSelect()->group('rule_id');
		
	    $rules->getSelect()->order('to_date');	    
          
        $rules->getSelect()->joinInner(array('display' => Mage::getSingleton('core/resource')->getTableName('aitoc_salesrule_display')), 'main_table.rule_id = display.rule_id', array('coupone_enable'));
        
        $rules->getSelect()->joinInner(array('d_title' => Mage::getSingleton('core/resource')->getTableName('aitoc_salesrule_display_title')), 'main_table.rule_id = d_title.rule_id AND store_id = ' . $iDefaultStoreId, array('value'));
		
        $rules->load();
		
        if ($iStoreId != $iDefaultStoreId)
        {
            foreach ($rules as $rule) 
            {
                $oSelect = $oDb->select();
            	
                $oSelect->from(array('salesrule' => Mage::getSingleton('core/resource')->getTableName('aitoc_salesrule_display_title')), array('value'))
                        ->where('salesrule.rule_id = "' . $rule->getRuleId() . '" AND store_id = ' . $iStoreId)
                ;
            	
                $sValue = $oDb->fetchOne($oSelect);

                if ($sValue)
                {
                    $rule->setValue($sValue);
                }
                
            }
        }
		
				
		
        foreach ($rules as $rule) 
        {
            if ($rule->getIsValid() === false) {
                continue;
            }
			
            if ($rule->getIsValid() !== true) {
                /**
                 * too many times used in general
                 */
                if ($rule->getUsesPerCoupon() && ($rule->getTimesUsed() >= $rule->getUsesPerCoupon())) {
                    $rule->setIsValid(false);
                    continue;
                }
                /**
                 * too many times used for this customer
                 */
                $ruleId = $rule->getId();
                if ($ruleId && $rule->getUsesPerCustomer()) {
                    
                    $ruleCustomer = Mage::getModel('salesrule/rule_customer');
                    
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
                    if ($ruleCustomer->getId()) {
                        if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                            continue;
                        }
                    }
                }
				
                $rule->afterLoad();
                
                /**
                 * passed all validations, remember to be valid
                 */
                $rule->setIsValid(true);
            }
            
            $discountAmount = 0;
            $baseDiscountAmount = 0;
            switch ($rule->getSimpleAction()) {
                case 'to_percent':

                case 'by_percent':
                    break;

                    /* AITOC modifications */
                case 'by_percent_surcharge':
                    
                    $rule->setIsSurcharge(true);
                	break;
                	
                case 'by_fixed_surcharge':
                    $rule->setIsSurcharge(true);
                	break;
                case 'cart_fixed_surcharge':
                    $rule->setIsSurcharge(true);
                    break;
                	/* AITOC modifications end */
                    
                case 'to_fixed':
                    break;

                case 'by_fixed':
                    break;

                case 'cart_fixed':
                    break;

                case 'buy_x_get_y':
                    break;
            }
            
            if ($rule->getCouponCode() && ( strtolower($rule->getCouponCode()) == strtolower($this->getCouponCode()))) {
                $address->setCouponCode($this->getCouponCode());
            }
            $rule->setIsLoyaltyValid($this->validate($rule));
                
            if (strpos($rule->getSimpleAction(), 'percent'))
            {
                $nAmount = $rule->getDiscountAmount();
                
                if (round($nAmount) == $nAmount)
                {
                    $rule->setDiscountAmount(round($nAmount));
                }
                else 
                {
                    $rule->setDiscountAmount(rtrim($nAmount, '0'));
                }
            }
        }
        if(version_compare(Mage::getVersion(),'1.12.0.0','ge'))
        {
            $this->getCodeUsageArray();
        }
		
        return $rules;
    }
	
	
    public function validate($rule)
    {
        $oCond = $rule->getConditions();
        
        if (!$oCond->getConditions()) {
            return false;
        }
        
        if ('any' == $oCond->getAggregator())
            $bResult   = false;
        else 
            $bResult   = true;
        
        $bMeetCond = false;
            
        $object = new Varien_Object();

        foreach ($oCond->getConditions() as $cond) {
        	if ($cond instanceof Aitoc_Aitloyalty_Model_Rule_Condition_Customer 
        	    or 
        	    $cond instanceof Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Combine)
        	{
        		$bMeetCond = true;
        		
        		if ('any' == $oCond->getAggregator())
        		{
        			// any aggregator
        		    $bResult = $bResult || ($cond->validate($object) || false);
        		} else 
        		{
        			// all aggregator
        			$bResult = $bResult && ($cond->validate($object) || false);
        		}
        	}
        }
        
        if (!$bMeetCond)
        {
        	$bResult = false;
        }

        return $bResult;
    }
    
	
	function myTestQry(){
		$oDb     = Mage::getModel('sales_entity/order')->getReadConnection();
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql        = "SELECT `main_table`.rule_id, `main_table`.name, `main_table`.description, `main_table`.from_date, `main_table`.to_date, `main_table`.uses_per_customer, `main_table`.is_active, `main_table`.simple_action, `main_table`.discount_amount, `main_table`.coupon_type, `main_table`.use_auto_generation, `main_table`.uses_per_coupon, `rule_coupons`.coupon_id, `rule_coupons`.code, `rule_coupons`.usage_limit, `rule_coupons`.usage_per_customer, `rule_coupons`.customer_id, `rule_coupons`.customer_email, `sc`.times_used, `rule_coupons`.expiration_date, `rule_coupons`.is_primary, `rule_coupons`.created_at type, `rc`.entity_id, `salesrule_customer_group`.customer_group_id, `rc`.customer_id, `rc`.coupon_code, `display`.`coupone_enable`, `d_title`.`value` 
						FROM `salesrule` AS `main_table` LEFT JOIN `salesrule_coupon` AS `rule_coupons` ON main_table.rule_id = rule_coupons.rule_id LEFT JOIN `salesrule_website` ON `main_table`.`rule_id`=`salesrule_website`.`rule_id` LEFT JOIN `salesrule_customer_group` ON `main_table`.`rule_id`=`salesrule_customer_group`.`rule_id` LEFT JOIN `aitoc_salesrule_assign_cutomer` AS `rc` ON main_table.rule_id = rc.entity_id AND rc.customer_id = 2710 AND (rc.coupon_code = rule_coupons.code) LEFT JOIN `salesrule_customer` AS `sc` ON sc.rule_id = rc.entity_id AND sc.customer_id = 2710 INNER JOIN `aitoc_salesrule_display` AS `display` ON main_table.rule_id = display.rule_id INNER JOIN `aitoc_salesrule_display_title` AS `d_title` ON main_table.rule_id = d_title.rule_id AND store_id = 1 
						WHERE (is_active=1) AND (salesrule_website.website_id = 1) AND ((rc.entity_id IS NOT NULL) OR (salesrule_customer_group.customer_group_id=1)) AND (from_date is null or from_date<='2013-05-21') AND (to_date is null or to_date>='2013-05-21') ORDER BY `to_date` ASC";
		
		$sql = "SELECT `main_table`.*, `rule_coupons`.`code`, `rule_coupons`.`usage_limit`, `rule_coupons`.`usage_per_customer`, `sc`.`times_used`, `rule_coupons`.`expiration_date`, `rule_coupons`.`is_primary`, `rule_coupons`.`created_at`, `salesrule_website`.*, `salesrule_customer_group`.*, `rc`.*, `display`.`coupone_enable`, `d_title`.`value` FROM `salesrule` AS `main_table` LEFT JOIN `salesrule_coupon` AS `rule_coupons` ON main_table.rule_id = rule_coupons.rule_id LEFT JOIN `salesrule_website` ON `main_table`.`rule_id`=`salesrule_website`.`rule_id` LEFT JOIN `salesrule_customer_group` ON `main_table`.`rule_id`=`salesrule_customer_group`.`rule_id` LEFT JOIN `aitoc_salesrule_assign_cutomer` AS `rc` ON main_table.rule_id = rc.entity_id AND rc.customer_id = 2710 AND (rc.coupon_code = rule_coupons.code) LEFT JOIN `salesrule_customer` AS `sc` ON sc.rule_id = rc.entity_id AND sc.customer_id = 2710 INNER JOIN `aitoc_salesrule_display` AS `display` ON main_table.rule_id = display.rule_id INNER JOIN `aitoc_salesrule_display_title` AS `d_title` ON main_table.rule_id = d_title.rule_id AND store_id = 1 WHERE (is_active=1) AND (salesrule_website.website_id = 1) AND ((salesrule_customer_group.customer_group_id IS NULL) OR (salesrule_customer_group.customer_group_id=1)) AND (from_date is null or from_date<='2013-05-21') AND (to_date is null or to_date>='2013-05-21') ORDER BY `to_date` ASC";
		
		//salesrule_customer
		
		
		$rows       = $connection->fetchAll($sql); //fetchRow($sql), fetchOne($sql),...
		Zend_Debug::dump($rows);
		
		print "<br><br>".__LINE__;die;
		
		//`main_table`.rule_id, `main_table`.name, `main_table`.description, `main_table`.from_date, `main_table`.to_date, `main_table`.uses_per_customer, `main_table`.is_active, `main_table`.simple_action, `main_table`.discount_amount, `main_table`.times_used, `main_table`.coupon_type, `main_table`.use_auto_generation, `main_table`.uses_per_coupon, 
		//`rule_coupons`.coupon_id, `rule_coupons`.code, `rule_coupons`.usage_limit, `rule_coupons`.usage_per_customer, `rule_coupons`.customer_id, `rule_coupons`.customer_email, `rule_coupons`.times_used, `rule_coupons`.expiration_date, `rule_coupons`.is_primary, `rule_coupons`.created_at type, 
		//`rc`.entity_id, `rc`.customer_id, `rc`.coupon_code, 
		//`display`.`coupone_enable`, `d_title`.`value`
	}
} } 