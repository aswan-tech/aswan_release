<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Quote/Titles.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ kYZjDDkNdekPfBQd('fca0a837556302f926a88d46baa4940d'); ?><?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/


class Aitoc_Aitloyalty_Block_Quote_Titles implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
	    $sHtml = '';
	    
        $sHtml .= '
    <tr>
        <td class="label"><label for="rule_aitloyalty_customer_display_title">' . Mage::helper('salesrule')->__('Please describe the rule conditions') .'</label></td>
        <td class="value" colspan="2">
    <div class="box" id="aitloyalty_customer_display_title" style="overflow:visible;">
		    <div class="hor-scroll" style="overflow:visible;">
	        <table class="dynamic-grid" cellspacing="0" id="attribute-labels-table">
	            <tr>';
        
        
$bFirst = true;
foreach ($this->getStores() as $_store)
{ 
    if ($_store->getId())
    {
        $sHtml .=  '<th>' . $_store->getName(); 
        
        if ($bFirst) 
        {
            $bFirst = false; 
            $sHtml .=  '<span class="required">*</span>';
        }
        $sHtml .=  '</th>';
    }	                
}    

$sHtml .= '</tr><tr>';
	
$_titles = $this->getTitleValues($element->getRule()); 

$bFirst = true;

foreach ($this->getStores() as $_store)
{
    if ($_store->getId())
    {
        if ($bFirst)
        {
            $sRequired = ' required-entry';
            $bFirst = false;
        }
        else 
        {
            $sRequired = '';
        }
        
//        $sHtml .= '<td><input class="input-text ' . $sRequired . '" type="text" id="aitloyalty_customer_display_titles_' . $_store->getId() . '" name="aitloyalty_customer_display_titles[' . $_store->getId() . ']" value="' . $_titles[$_store->getId()] . '" /></td>';
        $sHtml .= '<td><textarea class="input-text ' . $sRequired . '" id="aitloyalty_customer_display_titles_' . $_store->getId() . '" name="aitloyalty_customer_display_titles[' . $_store->getId() . ']" >' . $_titles[$_store->getId()] . '</textarea>';
    }
}
	                
$sHtml .=  '</tr></table></div></div></td></tr>';	    
	    
	    return $sHtml;
	}
	
    public function getStores()
    {
        $stores = Mage::getModel('core/store')
            ->getResourceCollection()
            ->setLoadDefault(true)
            ->load();
            
        return $stores;
    }
	
    public function getTitleValues($oModule)
    {
        $values = array();
        
		$translations = $oModule->getAitloyaltyCustomerDisplayTitles();
		
        foreach ($this->getStores() as $store) {
            if ($store->getId() != 0 AND !isset($values[$store->getId()])) {
                $values[$store->getId()] = isset($translations[$store->getId()]) ? $translations[$store->getId()] : '';
            }
        }
        
        return $values;
    }
    
	
} } 