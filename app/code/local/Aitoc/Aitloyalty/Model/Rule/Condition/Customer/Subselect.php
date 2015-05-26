<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Model/Rule/Condition/Customer/Subselect.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ jSgmMMjIEyjZUkcE('d840a4fa0605c741c4b4f79ffb4d70cb'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Subselect extends Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Subselect')
            ->setValue(null);
    }

    public function loadArray($arr, $key='conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    public function asXml($containerKey='conditions', $itemKey='condition')
    {
        $xml .= '<attribute>'.$this->getAttribute().'</attribute>'
            .'<operator>'.$this->getOperator().'</operator>'
            .parent::asXml($containerKey, $itemKey);
        return $xml;
    }

    public function loadAttributeOptions()
    {
        $hlp = Mage::helper('salesrule');
        $this->setAttributeOption(array(
            'customer'  => $hlp->__('Customer'),
        ));
        return $this;
    }
    
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('rule')->__('suits'),
        ));
        return $this;
    }
    
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('salesrule')->__("If a %s %s %s of these conditions:",
              $this->getAttributeElement()->getHtml(),
              $this->getOperatorElement()->getHtml(),
//              $this->getValueElement()->getHtml(),
              $this->getAggregatorElement()->getHtml()
           );
           if ($this->getId()!='1') {
               $html.= $this->getRemoveLinkHtml();
           }
        return $html;
    }
    
    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }
        
        if ('any' == $this->getAggregator())
            $bResult   = false;
        else 
            $bResult   = true;
        
        $bMeetCond = false;
            
        foreach ($this->getConditions() as $cond) {
        	if ($cond instanceof Aitoc_Aitloyalty_Model_Rule_Condition_Customer 
        	    or 
        	    $cond instanceof Aitoc_Aitloyalty_Model_Rule_Condition_Customer_Combine)
        	{
        		
        		$bMeetCond = true;
        		
        		if ('any' == $this->getAggregator())
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

} } 