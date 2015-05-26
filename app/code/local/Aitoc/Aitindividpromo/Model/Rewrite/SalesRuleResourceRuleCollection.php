<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Model/Rewrite/SalesRuleResourceRuleCollection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ TUDeZrDeahhUsZri('43d9c1873a11f579e3fd34c8bfcebedf'); ?><?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Model_Rewrite_SalesRuleResourceRuleCollection extends Mage_SalesRule_Model_Resource_Rule_Collection
{
    public function getSelectCountSql() 
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        
        // Count doesn't work with group by columns keep the group by
        if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) 
        {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
        } 
        else 
        {
            $countSelect->columns('COUNT(*)');
        }
        
        return $countSelect;
    } 

   
    public function setValidationFilter($websiteId, $customerGroupId, $couponCode='', $now=null)
    {
        
        /* */
        $iStoreId = Mage::app()->getStore()->getId();
        $iSiteId  = Mage::app()->getWebsite()->getId();

        $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitindividpromo')->getLicense()->getPerformer();
        $ruler     = $performer->getRuler();
        if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
        {
	        return parent::setValidationFilter($websiteId, $customerGroupId, $couponCode);
        }
		/* */
        
        if(version_compare(Mage::getVersion(), '1.12.0.0', '>='))
		    return parent::setValidationFilter($websiteId, $customerGroupId, $couponCode, $now);
		else
		    return $this->_setValidationFilter14($websiteId, $customerGroupId, $couponCode, $now);
    }
	
	protected function _setValidationFilter14($websiteId, $customerGroupId, $couponCode='', $now=null)
	{
        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }

        $this->addBindParam('code', $couponCode);
        
        $this->getSelect()->where('is_active=1');
        $this->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);
#        $this->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);

        $iCustomerId = Mage::getSingleton('customer/session')->getCustomerId();

		$oResource = Mage::getSingleton('core/resource');
		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        
        
        if ($sTable)
        {
            if (!$iCustomerId) 
            {
                $iCustomerId = 0;
                
                // fix for create order by admin
                
                if ($_SESSION AND isset($_SESSION['adminhtml_quote']) AND isset($_SESSION['adminhtml_quote']['customer_id']) AND $_SESSION['adminhtml_quote']['customer_id'])
                {
                    $iCustomerId = $_SESSION['adminhtml_quote']['customer_id'];
                }
            }
            
            $this->getSelect()->joinLeft(array('rc' => $sTable), 'main_table.rule_id = rc.entity_id AND rc.customer_id = ' . $iCustomerId);
            
#            $sWhere = '(customer_group_ids = "individ" AND rc.entity_id IS NOT NULL) OR (customer_group_ids != "individ" AND find_in_set("' . (int)$customerGroupId . '", customer_group_ids))';
            $sWhere = '(rc.entity_id IS NOT NULL) OR (find_in_set("' . (int)$customerGroupId . '", customer_group_ids))';
    
            $this->getSelect()->where($sWhere);
        }
        
        if (empty($couponCode)) {
            $this->getSelect()->where("code is null or code=''");
        }
        else {
            $this->getSelect()->where("code is null or code='' or code=:code");
        }
        $this->getSelect()->where('from_date is null or from_date<=?', $now);
        $this->getSelect()->where('to_date is null or to_date>=?', $now);
	    $this->getSelect()->order('sort_order');

	    return $this;	
	}
	
	    /**
	 * used only for Magento >= 1.7
	 *
     * Filter collection by website(s), customer group(s) and date.
     * Filter collection to only active rules.
     * Sorting is not involved
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string|null $now
     * @use $this->addWebsiteFilter()
     *
     * @return Mage_SalesRule_Model_Mysql4_Rule_Collection
     */
    public function addWebsiteGroupDateFilter($websiteId, $customerGroupId, $now = null)
    {
	    $oResource = Mage::getSingleton('core/resource');
	    $sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        
        if (!$sTable)
		{
		    return parent::addWebsiteGroupDateFilter($websiteId, $customerGroupId, $now);
		}
		
        if (!$this->getFlag('website_group_date_filter')) {
            if (is_null($now)) {
                $now = Mage::getModel('core/date')->date('Y-m-d');
            }
			$this->addWebsiteFilter($websiteId);

            $entityInfo = $this->_getAssociatedEntityInfo('customer_group');
            $connection = $this->getConnection();

			
			$iCustomerId = Mage::getSingleton('customer/session')->getCustomerId();
            if (!$iCustomerId) 
            {
                $iCustomerId = 0;
                // fix for create order by admin
                if ($_SESSION AND isset($_SESSION['adminhtml_quote']) AND isset($_SESSION['adminhtml_quote']['customer_id']) AND $_SESSION['adminhtml_quote']['customer_id'])
                {
                    $iCustomerId = $_SESSION['adminhtml_quote']['customer_id'];
                }
            }
            
            $this->getSelect()->joinLeft(array('rc' => $sTable), 'main_table.rule_id = rc.entity_id AND rc.customer_id = ' . $iCustomerId);
			
			
			/* coupon code date is checked here to skip the promotion expiry date in case the coupon is set which has a different expiry */
			
			$_coupon_code = Mage::getSingleton('checkout/session')->getQuote()->getCouponCode();
			
			$use_promotion_expiry = true;
			
			if(isset($_coupon_code)){
				$coupon_object = Mage::getModel("salesrule/coupon")->loadByCode($_coupon_code);
			
				$coupon_data = $coupon_object->getData();
				
				if(!empty($coupon_data)){
					$coupon_expiry = $coupon_object->getExpirationDate();
					
					$current_date = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
					
					if($coupon_expiry >= $current_date){
						$use_promotion_expiry = false;
					}
				}
			}
			
			/* coupon code date is checked here to skip the promotion expiry date in case the coupon is set which has a different expiry */    
			
			if($use_promotion_expiry){
				$this->getSelect()
					->joinLeft(
						array('customer_group_ids' => $this->getTable($entityInfo['associations_table'])),
						$connection->quoteInto(
							'main_table.' . $entityInfo['rule_id_field']
								. ' = customer_group_ids.' . $entityInfo['rule_id_field']
								. ' AND customer_group_ids.' . $entityInfo['entity_id_field'] . ' = ?',
							(int)$customerGroupId
						),
						array()
					)
					->where('from_date is null or from_date <= ?', $now)
					->where('to_date is null or to_date >= ?', $now)
					->where('(rc.entity_id IS NOT NULL) OR (customer_group_ids.customer_group_id = ?)', $customerGroupId);
			}else{
				$this->getSelect()
                ->joinLeft(
                    array('customer_group_ids' => $this->getTable($entityInfo['associations_table'])),
                    $connection->quoteInto(
                        'main_table.' . $entityInfo['rule_id_field']
                            . ' = customer_group_ids.' . $entityInfo['rule_id_field']
                            . ' AND customer_group_ids.' . $entityInfo['entity_id_field'] . ' = ?',
                        (int)$customerGroupId
                    ),
                    array()
                )
                ->where('from_date is null or from_date <= ?', $now)
                ->where("((to_date is null or to_date >= ?) AND (coupon_type=1)) OR (coupon_type!=1)", $now)
				->where('(rc.entity_id IS NOT NULL) OR (customer_group_ids.customer_group_id = ?)', $customerGroupId);
			}
            
            $this->addIsActiveFilter();
            $this->setFlag('website_group_date_filter', true);
        }

        return $this;
    }
} } 