<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Observer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ ESaDZZEftjEWAeNt('db0aae4cdbb68e8fe2df4669ad6480f7'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Observer
{
    public function __construct()
    {
    }  
    
    public function onModelSaveBefore($observer)
    {
    	$model = $observer->getObject();
    	if ($model instanceof Mage_SalesRule_Model_Rule)
    	{
    		if (isset($_POST['rule']))
    		{
	    		$aConditions = array(unserialize($model->getData('conditions_serialized')));
	    		$aPostConds  = $this->_convertFlatToRecursive($_POST['rule']);
	    		$aPostConds  = $aPostConds['conditions'];
	    		
	    		$this->_processValues($aConditions, $aPostConds);
	    		
	    		$model->setData('conditions_serialized', serialize($aConditions[0]));
    		}
    	}
    }
    
    public function onModelSaveAfter($observer)
    {
    	$model = $observer->getObject();
    	if ($model instanceof Mage_SalesRule_Model_Rule)
    	{
    		if (isset($_POST['rule']))
    		{
        		$oResource = Mage::getSingleton('core/resource');
        		$sDisplayTable = $oResource->getTableName('aitoc_salesrule_display');        
        		$sTitlesTable = $oResource->getTableName('aitoc_salesrule_display_title');        
        
        		// delete items
        		
                $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
                
                $oDb->delete($sDisplayTable, 'rule_id = ' . $model->getRuleId());

        		// delete titles

                $oDb->delete($sTitlesTable, 'rule_id = ' . $model->getRuleId());

                $oReq = Mage::app()->getFrontController()->getRequest();
            
                $data = $oReq->getPost();
    
                if ($data)
                {
                    if (!empty($data['aitloyalty_customer_display_enable']))
                    {
                        Mage::helper('aitloyalty/legal')->setHasLoyaltyFeatures();

                        $aDBInfo = array
                        (
                            'rule_id'           => $model->getRuleId(),
                            'coupone_enable'    => $data['aitloyalty_customer_display_coupon'],
                        );
                
                        $oDb->insert($sDisplayTable, $aDBInfo);
                        
                        // insert titles
                        
                        if (!empty($data['aitloyalty_customer_display_titles']))
                        {
                            foreach ($data['aitloyalty_customer_display_titles'] as $iStoreId => $sValue)
                            {
                                $aDBInfo = array
                                (
                                    'rule_id'   => $model->getRuleId(),
                                    'store_id'  => $iStoreId,
                                    'value'     => $sValue,
                                );
                        
                                $oDb->insert($sTitlesTable, $aDBInfo);
                            }
                        }
                    }
                }
    		}

            /* */
            if (Mage::helper('aitloyalty/legal')->getHasLoyaltyFeatures())
            {
                $license        = Aitoc_Aitsys_Abstract_Service::get()->platform()->getModule('Aitoc_Aitloyalty')->getLicense();
                $rulesInfo      = Mage::helper('aitsys/license')->getRulesInfo($license);
				$licensedStores = Mage::getStoreConfig('aitsys/modules/Aitoc_Aitloyalty');
				if(!is_array($licensedStores))
				{
					$licensedStores = explode(',', $licensedStores);
                }
				$websiteIds = $model->getWebsiteIds();
				if(!is_array($websiteIds))
				{
					$websiteIds     = explode(',', $websiteIds);
                }
                array_push($licensedStores, '');
              
                if (!$license->isInstalled())
                {
                    Mage::helper('aitloyalty/legal')->setIsNotifyRestrictedFeatures();
                }
                elseif (null !== $rulesInfo['store']['licensed'] && $rulesInfo['store']['licensed'] < $rulesInfo['store']['total'] && count(array_diff($websiteIds, $licensedStores)))
                {
                    Mage::helper('aitloyalty/legal')->setIsNotifyRestrictedFeatures();
                }
            }

            if (Mage::helper('aitloyalty/legal')->getIsNotifyRestrictedFeatures())
            {
                $session = Mage::getSingleton('core/session');
                $session->addWarning(
                    Mage::helper('aitloyalty')->__('Aitoc Loyalty Program functionality is disabled for some stores in websites you specified')
                    );
            }
            /**/
            
    	}
    }
    
    /**
     * Recursive.
     * Changes model values to contain period data
     *
     * @param array $aConditions
     * @param array $aPostConds
     * 
     */
    protected function _processValues(&$aConditions, $aPostConds)
    {
    	// this is in order to make numeric keys similar in both arrays
    	$aConditions = array_values($aConditions);
    	$aPostConds  = array_values($aPostConds);
    	
    	foreach ($aConditions as $key => $aCond)
    	{
    		if (isset($aCond['conditions']))
    		{
    			if (isset($aPostConds[$key]['conditions']) and isset($aConditions[$key]['conditions']))
    			{
	    			$this->_processValues($aConditions[$key]['conditions'], 
	    			                      $aPostConds[$key]['conditions']);
    			}
    		} else 
    		{
    			switch ($aConditions[$key]['attribute'])
    			{
    				case 'membership_period':
                        Mage::helper('aitloyalty/legal')->setHasLoyaltyFeatures();
    					$aConditions[$key]['value'] = $aPostConds[$key]['period_length'] . '---' . $aPostConds[$key]['period_type'];
    					break;
    			    case 'amount_during_period':
    			    case 'amount_average':
                        Mage::helper('aitloyalty/legal')->setHasLoyaltyFeatures();
                        $aConditions[$key]['value'] = $aConditions[$key]['value'] . '---' . $aPostConds[$key]['period_length'] . '---' . $aPostConds[$key]['period_type'];
                        break;
    			}
    		}
    	}
    }
    
    /**
     * Taken from Mage/Rule/Model/Rule and modified ([$path[$i]] to [$path[$i]-1])
     *
     * @param array $rule
     * @return array
     */
    protected function _convertFlatToRecursive(array $rule)
    {
        $arr = array();
        foreach ($rule as $key=>$value) {
            if (($key==='conditions' || $key==='actions') && is_array($value)) {
                foreach ($value as $id=>$data) {
                    $path = explode('--', $id);
                    $node =& $arr;
                    for ($i=0, $l=sizeof($path); $i<$l; $i++) {
                        if (!isset($node[$key][$path[$i]-1])) {
                            $node[$key][$path[$i]-1] = array();
                        }
                        $node =& $node[$key][$path[$i]-1];
                    }
                    foreach ($data as $k=>$v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                /**
                 * convert dates into Zend_Date
                 */
                if (in_array($key, array('from_date', 'to_date')) && $value) {
                    $value = Mage::app()->getLocale()->date(
                        $value,
                        Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                        null,
                        false
                    );
                }
                //$this->setData($key, $value);
            }
        }
        return $arr;
    }
    
    public function onValidateSalesRule($observer)
    {
        $rule       = $observer->getRule();
        $item       = $observer->getItem();
        $quote      = $observer->getQuote();
        $qty        = $observer->getQty();
        $address    = $observer->getAddress();
        $model = Mage::registry('aitFrontSalesRuleValidator');

        /**
         * The original source see in Mage_SalesRule_Model_Validator::process() 
         */
        // Magento version >= 1.4.2.0
        if (method_exists($model, "_getItemPrice") && method_exists($model, "_getItemBasePrice")) {

            $itemPrice              = $model->ait_getItemPrice($item);
            $baseItemPrice          = $model->ait_getItemBasePrice($item);
       
        // Magento version < 1.4.2.0
        } else {
            $itemPrice  = $item->getDiscountCalculationPrice();
            if ($itemPrice !== null) {
                $baseItemPrice = $item->getBaseDiscountCalculationPrice();
            } else {
                $itemPrice = $item->getCalculationPrice();
                $baseItemPrice = $item->getBaseCalculationPrice();
            }
        } 
        
        $rulePercent = min(100, $rule->getDiscountAmount());
        switch ($rule->getSimpleAction()) {
            case 'by_percent_surcharge':
                if ($step = $rule->getDiscountStep()) {
                    $qty = floor($qty/$step)*$step;
                }
                $discountAmount    = - ($qty * $itemPrice - $item->getDiscountAmount()) * $rulePercent / 100;
                $baseDiscountAmount= - ($qty * $baseItemPrice - $item->getBaseDiscountAmount()) * $rulePercent / 100; 

                if (!$rule->getDiscountQty() || $rule->getDiscountQty()>$qty) {
                    $discountPercent = $item->getDiscountPercent()+$rulePercent;
                    $item->setDiscountPercent($discountPercent);
              
                }
                break;

            case 'by_fixed_surcharge':
                if ($step = $rule->getDiscountStep()) {
                    $qty = floor($qty/$step)*$step;
                }
                $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                $discountAmount    = -$qty*$quoteAmount;
                $baseDiscountAmount= -$qty*$rule->getDiscountAmount();
                break;
            case 'cart_fixed_surcharge':
                $cartRules = $address->getCartFixedRules();
                if (!isset($cartRules[$rule->getId()])) 
                {
                    $cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
                    $discountAmount = 0-$quoteAmount;
                    $baseDiscountAmount = 0-$cartRules[$rule->getId()];
                    $address->setCartFixedRules($cartRules);
                }
                else
                {
                // apply surcharge only once per order
                    $discountAmount = 0;
                    $baseDiscountAmount = 0;
                }
                break;
            default:
                return $this;
        }

            $discountAmount     = $quote->getStore()->roundPrice($discountAmount);
            $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            $discountAmount     = min($item->getDiscountAmount()+$discountAmount, $item->getRowTotal());
            $baseDiscountAmount = min($item->getBaseDiscountAmount()+$baseDiscountAmount, $item->getBaseRowTotal());

            $item->setDiscountAmount($discountAmount);
            $item->setBaseDiscountAmount($baseDiscountAmount);

            switch ($rule->getSimpleFreeShipping()) {
                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM:
                    $item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
                    break;

                case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS:
                    $address->setFreeShipping(true);
                    break;
            }

            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            if ($rule->getCouponCode() && ( strtolower($rule->getCouponCode()) == strtolower($model->getCouponCode()))) {
                $address->setCouponCode($model->getCouponCode());
            }
            
            $model->ait_addDiscountDescription($address, $rule);
            
            if ($rule->getStopRulesProcessing()) {
                break;
            }

        $item->setAppliedRuleIds(join(',',$appliedRuleIds));
        $address->setAppliedRuleIds($model->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($model->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));
  
    }
} } 