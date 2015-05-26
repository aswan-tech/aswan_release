<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Model/Rewrite/FrontSalesRuleMysql4RuleCollection.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ cCBMeDBMjooCseDq('b6108d981d99013e872baf6cd162877d'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Model_Rewrite_FrontSalesRuleMysql4RuleCollection extends Mage_SalesRule_Model_Mysql4_Rule_Collection
{
    public function setValidationFilter($websiteId, $customerGroupId, $couponCode='', $now=null)
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
        
//        echo $this->getSelect()->__toString(); exit;
#d($this->getSelect()->__toString(), 1);
	    return $this;
    }
} } 