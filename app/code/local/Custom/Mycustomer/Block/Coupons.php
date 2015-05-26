<?php
class Custom_Mycustomer_Block_Coupons extends Mage_Customer_Block_Account_Dashboard
{
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
		
}
