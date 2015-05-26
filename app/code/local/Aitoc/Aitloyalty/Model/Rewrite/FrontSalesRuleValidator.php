<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rewrite/FrontSalesRuleValidator.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ PYrZaaPAlDPOXjfl('e5b813e8a1b34fc1606cfb433ff014de'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Rewrite_FrontSalesRuleValidator extends Mage_SalesRule_Model_Validator
{
    private static $_isUseCustomActions;

    protected function _canProcessRule($rule, $address) {
        if(!Mage::registry('aitFrontSalesRuleValidator'))
        {
            Mage::register('aitFrontSalesRuleValidator', $this);
        }
        
        return parent::_canProcessRule($rule, $address) && 
                !(!self::_isUseCustomActions() && in_array($rule->getSimpleAction(), array('by_percent_surcharge', 'by_fixed_surcharge', 'cart_fixed_surcharge')));
    }

    /**
     * Check if rule can be applied for custom actions
     *
     * @return  bool
     */
    protected static function _isUseCustomActions()
    {
        if (null === self::$_isUseCustomActions)
        {
            self::$_isUseCustomActions = true;
            $iStoreId = Mage::app()->getStore()->getId();
            $iSiteId  = Mage::app()->getWebsite()->getId();
            /* */
            $performer = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitloyalty')->getLicense()->getPerformer();
            $ruler     = $performer->getRuler();
            if (!($ruler->checkRule('store', $iStoreId, 'store') || $ruler->checkRule('store', $iSiteId, 'website')))
            {
                self::$_isUseCustomActions = false;
            }
            /* */
        }
        return self::$_isUseCustomActions;
    }
    
    // create publiñ
    public function ait_addDiscountDescription($address, $rule)
    {
       return $this->_addDiscountDescription($address, $rule);
    }
    
    // create publiñ
    public function ait_getItemPrice($item)
    {
       return $this->_getItemPrice($item);
    }
    
    // create publiñ
    public function ait_getItemBasePrice($item)
    {
       return $this->_getItemBasePrice($item);
    }
    
} } 