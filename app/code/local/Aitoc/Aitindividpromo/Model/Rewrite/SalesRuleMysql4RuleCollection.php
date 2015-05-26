<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Model/Rewrite/SalesRuleMysql4RuleCollection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ QRaDrEaDkicRsryp('24f0f10e2545edee69e4007040235890'); ?><?php
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Model_Rewrite_SalesRuleMysql4RuleCollection extends Mage_SalesRule_Model_Mysql4_Rule_Collection
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

        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }

        $this->addBindParam('code', $couponCode);
        
        $this->getSelect()->where('is_active=1');
        $this->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);

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
} } 