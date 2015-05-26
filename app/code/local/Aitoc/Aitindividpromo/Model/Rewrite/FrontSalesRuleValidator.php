<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Model/Rewrite/FrontSalesRuleValidator.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ NQrZkPrZyoIQskmq('8f7ca2a2aad15d8a9b1b86c294bdcebf'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Model_Rewrite_FrontSalesRuleValidator extends Mage_SalesRule_Model_Validator
{
    public function init($websiteId, $customerGroupId, $couponCode)
    {
        $this->setWebsiteId($websiteId)
           ->setCustomerGroupId($customerGroupId)
           ->setCouponCode($couponCode);
/*
        $this->_rules = Mage::getModel('salesrule/mysql4_rule_collection')
            ->setValidationFilter($websiteId, $customerGroupId, $couponCode)
            ->load();
*/

        $key = $websiteId . '_' . $customerGroupId . '_' . $couponCode;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = Mage::getResourceModel('salesrule/rule_collection')
#            $this->_rules[$key] = Mage::getModel('salesrule/mysql4_rule_collection')
                ->setValidationFilter($websiteId, $customerGroupId, $couponCode)
                ->load();
        }

        
        return $this;
    }
} } 