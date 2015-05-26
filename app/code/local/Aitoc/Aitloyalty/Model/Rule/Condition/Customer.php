<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rule/Condition/Customer.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ BSmkyyBCrrBjcahr('fcf4ff7f92cf9d56a58a0b170bdfcd3c'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Rule_Condition_Customer extends Mage_Rule_Model_Condition_Abstract
{
	protected $_iPeriodLength = null;
    
    private $_sPeriodType = null;
    
    private $_sValue = null;
	
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('rule')->__('is'),
            '!='  => Mage::helper('rule')->__('is not'),
            '>='  => Mage::helper('rule')->__('equals or greater than'),
            '<='  => Mage::helper('rule')->__('equals or less than'),
            '>'   => Mage::helper('rule')->__('greater than'),
            '<'   => Mage::helper('rule')->__('less than'),
//            '{}'  => Mage::helper('rule')->__('contains'),
//            '!{}' => Mage::helper('rule')->__('does not contain'),
//            '()'  => Mage::helper('rule')->__('is one of'),
//            '!()' => Mage::helper('rule')->__('is not one of'),
        ));
        $this->setOperatorByInputType(array(
            'string' => array('==', '!=', '>=', '>', '<=', '<', '{}', '!{}', '()', '!()'),
            'numeric' => array('==', '!=', '>=', '>', '<=', '<', '()', '!()'),
            'date' => array('==', '>=', '<='),
            'select' => array('==', '!='),
            'multiselect' => array('==', '!=', '{}', '!{}'),
            'grid' => array('()', '!()'),
        ));
        return $this;
    }
	
    protected function _init()
    {
    	$aValues = explode('---', $this->getValue());
    	if (is_array($aValues) and (count($aValues) > 1))
    	{
	    	switch ($this->getAttribute())
	        {
	        	case 'amount_during_period':
	            case 'amount_average':
	            	$this->_sValue        = $aValues[0];
	            	$this->_iPeriodLength = $aValues[1];
	            	$this->_sPeriodType   = $aValues[2];
	            	break;
	            case 'membership_period':
	            	$this->_sValue        = '';
	                $this->_iPeriodLength = $aValues[0];
	                $this->_sPeriodType   = $aValues[1];
	            	break;
	        }
    	} else 
    	{
    		$this->_sValue = $this->getValue();
    	}
    }
    
    public function asHtml()
    {
    	$this->setPrefix('conditions');
    	
        $this->_init();

        switch ($this->getAttribute())
    	{
    		case 'amount_during_period':
    		case 'amount_average':
    			$html = $this->getTypeElementHtml()
	           .$this->getAttributeElementHtml()
	           .$this->getAdditionalCondPeriodLengthHtml()
	           .$this->getAdditionalCondPeriodHtml()
	           .$this->getOperatorElementHtml()
	           .$this->getValueElementHtml()
	           .$this->getRemoveLinkHtml()
	           .$this->getChooserContainerHtml();
    			break;
    		case 'membership_period':
    			$html = $this->getTypeElementHtml()
               .$this->getAttributeElementHtml()
               .$this->getOperatorElementHtml()
               .$this->getAdditionalCondPeriodLengthHtml()
               .$this->getAdditionalCondPeriodHtml()
//               .$this->getValueElementHtml()
               .$this->getRemoveLinkHtml()
               .$this->getChooserContainerHtml();
    			break;
    	}
        return $html;
    }
    
    public function getValueElement()
    {
        $elementParams = array(
            'name'               => 'rule['.$this->getPrefix().']['.$this->getId().'][value]',
            'value'              => $this->_sValue,
            'values'             => $this->getValueSelectOptions(),
            'value_name'         => $this->_sValue,
            'after_element_html' => $this->getValueAfterElementHtml(),
            'explicit_apply'     => $this->getExplicitApply(),
        );
        if ($this->getInputType()=='date') {
            // date format intentionally hard-coded
            $elementParams['input_format'] = Varien_Date::DATE_INTERNAL_FORMAT;
            $elementParams['format']       = Varien_Date::DATE_INTERNAL_FORMAT;
        }
        return $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__value',
            $this->getValueElementType(),
            $elementParams
        )->setRenderer($this->getValueElementRenderer());
    }

    public function getValueElementHtml()
    {
        $sHtml = $this->getValueElement()->getHtml();
        $sHtml = str_replace('<a href="javascript:void(0)" class="label"></a>', '<a href="javascript:void(0)" class="label">...</a>', $sHtml);
        return $sHtml;
    }
    
    public function getAttributeElement()
    {
        $oElement = $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__attribute', 'select', array(
            'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][attribute]',
            'values'=>$this->getAttributeSelectOptions(),
            'value'=>$this->getAttribute(),
            'value_name'=>$this->getAttributeName(),
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));
        $oElement->setShowAsText(true);
        return $oElement;
    }

    public function getAttributeElementHtml()
    {
        return $this->getAttributeElement()->getHtml();
    }
	
    public function getAdditionalCondPeriodLengthHtml()
    {
    	return $this->getAdditionalCondPeriodLength()->getHtml();
    }
    
    public function getAdditionalCondPeriodLength()
    {
    	$aValues = array(
    	   '1' => '1',
    	   '2' => '2',
    	   '3' => '3',
    	   '4' => '4',
    	   '5' => '5',
    	   '6' => '6',
    	   '7' => '7',
    	   '8' => '8',
    	   '9' => '9',
    	   '10' => '10',
    	   '11' => '11',
    	   '12' => '12',
    	   '13' => '13',
    	   '14' => '14',
    	   '15' => '15',
    	   '16' => '16',
    	   '17' => '17',
    	   '18' => '18',
    	   '19' => '19',
    	   '20' => '20',
    	   '21' => '21',
    	   '22' => '22',
    	   '23' => '23',
    	   '24' => '24',
    	   '25' => '25',
    	   '26' => '26',
    	   '27' => '27',
    	   '28' => '28',
    	   '29' => '29',
    	   '30' => '30',
    	   '31' => '31',
    	   '32' => '32',
    	   '33' => '33',
    	   '34' => '34',
    	   '35' => '35',
    	   '36' => '36',
    	   '37' => '37',
    	   '38' => '38',
    	   '39' => '39',
    	   '40' => '40',
    	   '41' => '41',
    	   '42' => '42',
    	   '43' => '43',
    	   '44' => '44',
    	   '45' => '45',
    	   '46' => '46',
    	   '47' => '47',
    	   '48' => '48',
    	   '49' => '49',
    	   '50' => '50',
    	);
    	
        $elementParams = array(
            'name'               => 'rule['.$this->getPrefix().']['.$this->getId().'][period_length]',
            'value'              => $this->_iPeriodLength,
            'values'             => $aValues,
            'value_name'         => isset($aValues[$this->_iPeriodLength]) ? $aValues[$this->_iPeriodLength] : '',
//            'after_element_html' => $this->getValueAfterElementHtml(),
//            'explicit_apply'     => $this->getExplicitApply(),
        );
        return $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__period_length',
            'select',
            $elementParams
        )->setRenderer($this->getValueElementRenderer());
    }
    
    public function getAdditionalCondPeriodHtml()
    {
        return $this->getAdditionalCondPeriod()->getHtml();
    }
    
    public function getAdditionalCondPeriod()
    {
        $aValues = array(
           'day'     => 'Days', 
           'week'    => 'Weeks',
           'month'   => 'Months',
           'year'    => 'Years',
           'time'    => 'All Time',
        );
        if ('membership_period' == $this->getAttribute())
        {
        	unset($aValues['time']);
        }
        $elementParams = array(
            'name'               => 'rule['.$this->getPrefix().']['.$this->getId().'][period_type]',
            'value'              => $this->_sPeriodType,
            'values'             => $aValues,
            'value_name'         => isset($aValues[$this->_sPeriodType]) ? $aValues[$this->_sPeriodType] : '',
//            'after_element_html' => $this->getValueAfterElementHtml(),
//            'explicit_apply'     => $this->getExplicitApply(),
        );
        return $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__period_type',
            'select',
            $elementParams
        )->setRenderer($this->getValueElementRenderer());
    }
    
//    public function getValueElementRenderer()
//    {
//        if (strpos($this->getValueElementType(), '/')!==false) {
//            return Mage::getBlockSingleton($this->getValueElementType());
//        }
//        return Mage::getBlockSingleton('Aitoc_Aitloyalty_Block_Editable');
//    }
    
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['amount_during_period'] = Mage::helper('aitloyalty')->__('Amount spent during last');
        $attributes['membership_period'] = Mage::helper('salesrule')->__('Customer\'s membership period');
        $attributes['amount_average'] = Mage::helper('salesrule')->__('Average order during last');
    }
    
    /**
     * Load attribute options
     *
     * @return Aitoc_Aitloyalty_Model_Rule_Condition_Customer
     */
    public function loadAttributeOptions()
    {
        $attributes = array(
        );

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }
    
    public function checkCustomer($object)
    {
        if (Mage::app()->getStore()->isAdmin())
        {
            $iCustomerId = $object->getCustomerId();
        }
        else
        {
            $iCustomerId = Mage::getSingleton('customer/session')->getCustomerId();
        }
        return $iCustomerId;
    }
    
    public function validate(Varien_Object $object)
    {
    	$bReturn = false;
    	
    	$iCustomerId = $this->checkCustomer($object);
        
    	if (!$iCustomerId)
    	{
    		return false;
    	}

    	$oDb     = Mage::getModel('sales_entity/order')->getReadConnection();
    	/* @var $oDb Varien_Db_Adapter_Pdo_Mysql */
    	$oSelect = $oDb->select();
    	/* @var $oSelect Varien_Db_Select */
    	
    	switch ($this->getAttribute())
    	{
    		case 'amount_average':
    			$aValue = explode('---', $this->getValue()); // value is for example 20---2---day (20 is the the amount, and period is 2 days)
    			
    			if ('time' != $aValue[2])
    			{
	    			$sFromDate = strtotime('-' . $aValue[1] . ' ' . $aValue[2]);
	    			$sToDate   = time();
    			}
    			if(Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion(">=1.4.1.1"))
                {
                    $oSelect->from(array('sales' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order')),
                                  array(
                                      'avgsale'   => 'AVG( COALESCE(sales.base_total_paid, 0) - COALESCE(sales.base_total_refunded, 0) )', // COALESCE selects first not null value
                                  )
                              );                    
                }
                else
                {

                    $oSelect->from(array('sales' => Mage::getSingleton('core/resource')->getTableName('sales_order')),
                                  array(
                                      'avgsale'   => 'AVG(sales.base_total_paid - sales.base_total_refunded)',
                                  )
                              );                    
                }
                $oSelect
			            ->where('sales.customer_id = ?', $iCustomerId)
			            ->where('sales.base_total_paid > 0')
			    ;
			    if ('time' != $aValue[2])
			    {
				    $oSelect
				            ->where('sales.updated_at < ?', date('Y-m-d H:i:s', $sToDate))
				            ->where('sales.updated_at > ?', date('Y-m-d H:i:s', $sFromDate))
				    ;
			    }
			    $oSelect->group('sales.customer_id');
			    
			    $fAverage = $oDb->fetchOne($oSelect);
    	        if (false === $fAverage)
                {
                    $fAverage = 0;
                }
			    if ( (false !== $fAverage) and (false !== $aValue[0]) )
                {
				    $sEvalCond = '$bReturn = doubleval(' . $fAverage . ') ' . $this->getOperator() . ' doubleval(' . $aValue[0] . ');';
				    try{
                        eval($sEvalCond);
                    }
                    catch(Exception $e)
                    {
                        Mage::log("There is error in shopping cart price rule: ".$e->getMessage(), NULL, 'aitloyalty_log.txt');                            
                        $bReturn = false;
                    }
                }
    			break;
    		case 'membership_period':
                $aValue = explode('---', $this->getValue()); // value is for example 2---month (means 2 months)

                $oSelect->from(array('cust' => Mage::getSingleton('core/resource')->getTableName('customer_entity')),
                               array(
                                   'created_date' => 'cust.created_at',
                               )
                          );                    
                $oSelect->where('cust.entity_id = ?', $iCustomerId);
                
                $sCreatedOnDate = $oDb->fetchOne($oSelect);
                $sCreatedTS = strtotime($sCreatedOnDate);
				$sCompareTS = strtotime('-' . $aValue[0] . ' ' . $aValue[1]);
				if (false !== $sCompareTS and false !== $sCreatedTS)
                {
					/*if ($this->getOperator() == '==' or $this->getOperator() == '!=')
					{*/
					$sEvalCond = '$bReturn = ((int)date("Ymd", ' . $sCompareTS . ') ' . $this->getOperator() . ' (int)date("Ymd", ' . $sCreatedTS . ') );';
					/*}
					else
					{
						$sEvalCond = '$bReturn = (' . $sCompareTS . ' ' . $this->getOperator() . ' ' . $sCreatedTS . ');';
					}*/
					try{
                        eval($sEvalCond);
                    }
                    catch(Exception $e)
                    {
                        Mage::log("There is error in shopping cart price rule: ".$e->getMessage(), NULL, 'aitloyalty_log.txt');                            
                        $bReturn = false;
                    }
                }
                break;
    		case 'amount_during_period':
    			// this branch is almost the same as amount_average, but the select query takes SUM instead of AVG
                $aValue = explode('---', $this->getValue()); // value is for example 20---2---day (20 is the the amount, and period is 2 days)

                if (!empty($aValue[2]) AND 'time' != $aValue[2])
                {
	                $sFromDate = strtotime('-' . $aValue[1] . ' ' . $aValue[2]);
	                $sToDate   = time();
                }
                
                if(Aitoc_Aitsys_Abstract_Service::get()->isMagentoVersion(">=1.4.1.1"))
                {
                   $oSelect->from(array('sales' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order')),
                              array(
                                  'avgsale'   => 'SUM( COALESCE(sales.base_total_paid, 0) ) - SUM( COALESCE(sales.base_total_refunded, 0) )', // COALESCE selects first not null value
                              )
                          )
                        ->where('sales.customer_id = ?', $iCustomerId);
                }
                else
                {

                    $oSelect->from(array('sales' => Mage::getSingleton('core/resource')->getTableName('sales_order')),
                              array(
                                  'avgsale'   => 'SUM(sales.base_total_paid) - SUM(sales.base_total_refunded)', // COALESCE selects first not null value
                              )
                          )
                        ->where('sales.customer_id = ?', $iCustomerId);         
                }
                
                if (!empty($aValue[2]) AND 'time' != $aValue[2])
                {
                    $oSelect
                            ->where('sales.updated_at < ?', date('Y-m-d H:i:s', $sToDate))
                            ->where('sales.updated_at > ?', date('Y-m-d H:i:s', $sFromDate))
                    ;
                }
                $oSelect->group('sales.customer_id');
                $fAverage = $oDb->fetchOne($oSelect);
                if (false === $fAverage)
                {
                	$fAverage = 0;
                }
                if ( (false !== $fAverage) and (false !== $aValue[0]) )
                {
                	$sEvalCond = '$bReturn = doubleval(' . $fAverage . ') ' . $this->getOperator() . ' doubleval(' . $aValue[0] . ');';
                    try{
                        eval($sEvalCond);
                    }
                    catch(Exception $e)
                    {
                        Mage::log("There is error in shopping cart price rule: ".$e->getMessage(), NULL, 'aitloyalty_log.txt');                            
                        $bReturn = false;
                    }
                }
    			break;
    	}
    	
    	return $bReturn;
    }
    
} } 