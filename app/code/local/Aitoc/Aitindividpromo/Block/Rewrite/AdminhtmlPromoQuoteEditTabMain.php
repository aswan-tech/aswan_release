<?php
/**
 * Product:     Individual Promotions for Magento Enterpise Edition
 * Package:     Aitoc_Aitindividpromo_10.0.7_574525
 * Purchase ID: UjgdLvjpFE0u1HHQEOk2KNCXazbZ9kQjUnTtO4dMb0
 * Generated:   2013-05-13 06:35:45
 * File path:   app/code/local/Aitoc/Aitindividpromo/Block/Rewrite/AdminhtmlPromoQuoteEditTabMain.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitindividpromo')){ TUDeZrDeahhUsZri('18aca46d50e50ecdc806047b0f288154'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitindividpromo_Block_Rewrite_AdminhtmlPromoQuoteEditTabMain extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main
{
    protected function _prepareForm()
    {
        $oParent = parent::_prepareForm();
        
        $oModel = Mage::registry('current_promo_quote_rule');
        
        $oForm = $oParent->getForm();
        
        foreach ($oForm->getElements() as $aElement)
        {
            $aElement->removeField('customer_group_ids');
#            d(get_class_methods($aElement));
#            d($aElement->getElement('customer_group_ids'));
#            d($aElement->getElementHtml());
        }
        
        // set groups and individuals
         
        $fieldset = $oForm->addFieldset('assign_fieldset', array('legend'=>Mage::helper('salesrule')->__('Assign To')));
        
        $oAitindividpromo = Mage::getModel('aitindividpromo/aitindividpromo');
        
        $customerGroups = $oAitindividpromo->getCustomerGroups(true);
        
        /*
        $fieldset->addField('is_individ', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Assign Type'),
            'title'     => Mage::helper('salesrule')->__('Assign Type'),
            'name'      => 'is_individ',
            'required' => true,
            'options'    => array(
                '0' => Mage::helper('salesrule')->__('Customer Groups'),
                '1' => Mage::helper('salesrule')->__('Individual Customers'),
            ),
        ));
        */
        
        
        if ($oModel->getId())
        {
            $aData = $oModel->getData();
            
            $aGroupValues    = $aData['customer_group_ids'];
        }
        else 
        {
            $aGroupValues    = array();
        }
        
        
#        $oForm->addValues(array('is_individ' => $iAssignType));
        
        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('salesrule')->__('Customer Groups'),
            'title'     => Mage::helper('salesrule')->__('Customer Groups'),
//            'required'  => true,
            'required'  => false,
            'values'    => $customerGroups,
        ));

        $oForm->addValues(array('customer_group_ids' => $aGroupValues));
        
        /*
        $fieldset->addField('customer_individ_ids', 'multiselect', array(
            'name'      => 'customer_individ_ids[]',
            'label'     => Mage::helper('salesrule')->__('Individual Customers'),
            'title'     => Mage::helper('salesrule')->__('Individual Customers'),
            'required'  => true,
            'values'    => $aCustomerList,
        ));
        
        $oForm->addValues(array('customer_individ_ids' => $aCustomerValues));
        */
        
        $fieldset->addField('replace_placeholder', 'hidden', array(
            'name' => 'replace_placeholder',
        ));
        
        $fieldset->addField('customer_individ_ids', 'hidden', array(
            'name' => 'customer_individ_ids',
        ));
        
        
        return $oForm;
    }
    
    protected function _toHtml()
    {
        $sMainHtml = parent::_toHtml();
        
		$btnUrl = $this->getUrl('aitindividpromo/index/sendmail/id/'.$thisId, array('_current'=>true));
        $sDivCustomer = '
<style>


DIV.latest_clip_vertical div {
	height: 1px;
	padding: 0px;

	margin-top: 1px;	
	margin-bottom: 2px;	
	
	background:none;
	clear:both;
	line-height:0px;
	font-size:0px;
	border-bottom:1px solid #CFCFCF;
}

a.a_del {
}
</style>

<div class="latest_clip_vertical" id="customer_ids_div" style=" width:280px;
    height: 220px;
    overflow: auto; 
    overflow-y: scroll; 
    overflow-x: hidden;
	padding-top: 1px;	
   
    border: 1px solid #BCBCBC;
    ">
</div>  

<div align=center>
    <div id="customer_search_add">
        <br><a href="#" onClick="showCustomerSearch(true); return false;">' . Mage::helper('salesrule')->__('Add Customers') . '</a>
		
		<button style="" onclick="setLocation(\''.$btnUrl.'\')" class="scalable save" type="button" title="Send Mail to Customers"><span>Send Mail to Customers</span></button>
		
    </div>
    <div id="customer_search_hide">
        <br><a href="#" onClick="showCustomerSearch(false); return false;">' . Mage::helper('salesrule')->__('Hide Customers') . '</a>
		
		<button style="" onclick="setLocation(\''.$btnUrl.'\')" class="scalable save" type="button" title="Send Mail to Customers"><span>Send Mail to Customers</span></button>
    </div>
</div>

        ';
        
        $oBlock = $this->getLayout()->createBlock('aitindividpromo/customers');
        
        $sIndividHtml = '<td class="label">' . Mage::helper('salesrule')->__('Individual Customers') . '</td>
                        <td class="value">' . $sDivCustomer . '<br></td>
                        <td id="note_customer_group_ids"></td><tr><td></td><td></td><td width="100%"></td></tr><tr id="customer_search_tr"><td colspan="3">' . $oBlock->getHtml() . '</td></tr>';
        
        
        $sMainHtml = str_replace('<td colspan="2" class="hidden"><input id="rule_replace_placeholder" name="replace_placeholder" value="" type="hidden"/></td>', $sIndividHtml, $sMainHtml);
        
        return $sMainHtml . $this->_getAppendHtml();
    }

    protected function _getAppendHtml()
    {
        $sAppendHtml = '
            <script type="text/javascript">
            var aCustomerIdsHash    = [];
            var aCustomerNameHash   = [];
            
            iCounter = 1;
        ';
        
        $oModel = Mage::registry('current_promo_quote_rule');        
        
        if ($oModel->getId())
        {
            $aData = $oModel->getData();
            
            // get from aitoc table 
            
    		$oResource = Mage::getSingleton('core/resource');
    		$sTable = $oResource->getTableName('aitoc_salesrule_assign_cutomer');        
    
            $oDb = Mage::getSingleton('core/resource')->getConnection('core_write');
        
            $oSelect    = $oDb  ->select()
                                ->from($sTable, array('customer_id'))
                                ->where('entity_id = ' . $oModel->getId());
            
            $aCustomerValues = $oDb->fetchCol($oSelect);       
        }
        else 
        {
            $aCustomerValues = array();
        }
        
        $aCustomerList = array();
        
        if ($aCustomerValues)
        {
            // get customer list
            
            $aCustomerList = array();

            $oCustomerCollection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToFilter('entity_id', $aCustomerValues)
                ->load();
                                    
            foreach ($oCustomerCollection as $aCustomer)
            {
                $sCustomerFullName = $aCustomer->getName() . ' (' . $aCustomer->getEmail() .  ')';
                $aCustomerList[$aCustomer->getId()] = $sCustomerFullName;
            }
        }
        
        if ($aCustomerList)
        {
            $iCounter = 1;
            foreach ($aCustomerList as $iCustomerId => $sName)
            {
                $sAppendHtml .= '
                    aCustomerIdsHash[' . $iCounter . '] = ' . $iCustomerId . ';
                    aCustomerNameHash[' . $iCustomerId . '] = ' . json_encode($sName) . ';
                    iCounter = ' . $iCounter . ';
                ';
                $iCounter++;
            }
            
            $sAppendHtml .= '
                iCounter = ' . $iCounter . ';            
            ';
        }
        
        $sAppendHtml .= '

function showCustomerSearch(isVisible)
{
//    var tr = $(customer_search_tr).parentNode;
    var oTr         = $("customer_search_tr");
    var oDivAdd     = $("customer_search_add");
    var oDivHide    = $("customer_search_hide");

    if (isVisible) {
        oTr.show();
        
        oDivHide.show();
        
        oDivAdd.blur();
        oDivAdd.hide();
    } else {
        oTr.blur();
        oTr.hide();
        
        oDivAdd.show();
        
        oDivHide.blur();
        oDivHide.hide();
    }


}

showCustomerSearch(false);

function populateCustomers()
{
    if (!$("customer_ids_div") || !$("rule_customer_individ_ids")) return false;

    var sContent        = "";
    var sIndividValue   = "";
    
    for (i=1;i<=iCounter;i++)
    {
        if (aCustomerIdsHash[i])
        {
            sContent = sContent + "<a href=\'#\' onclick=\'deleteCustomer(" + i + "); return false;\' class=\'a_del\'><img src=\''.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/rule_component_remove.gif\' style=\'padding-top:3px;padding-left:3px;\'></a>&nbsp;&nbsp;" + aCustomerNameHash[aCustomerIdsHash[i]];
            
            sContent = sContent + "<div></div>";            
            
            sIndividValue = sIndividValue + "_" + aCustomerIdsHash[i];
        }    
    }    
    
    $("rule_customer_individ_ids").value = sIndividValue;

    $("customer_ids_div").innerHTML = sContent;
    
}

function deleteCustomer(iRecordId)
{
    if (!iRecordId) return false;

    if (aCustomerIdsHash[iRecordId])
    {
        aCustomerNameHash[aCustomerIdsHash[iRecordId]] = null;
        aCustomerIdsHash[iRecordId] = null;
    }
    
    populateCustomers();
}

populateCustomers();

function ________setRowVisibility(id, isVisible)
{
    if ($(id)) {
        var td = $(id).parentNode;
        var tr = $(td.parentNode);

        if (isVisible) {
            tr.show();
        } else {
            tr.blur();
            tr.hide();
        }
    }
}

function _________bindIndivid()
{
    if ($("rule_is_individ") && ($("rule_is_individ").selectedIndex))
    {
        setRowVisibility("rule_customer_group_ids", false);
        
        $("rule_customer_group_ids").removeClassName("required-entry");
        $("rule_customer_individ_ids").addClassName("required-entry");
        
        setRowVisibility("rule_customer_individ_ids", true);
    }
    else
    {
        setRowVisibility("rule_customer_group_ids", true);
        
        $("rule_customer_group_ids").addClassName("required-entry");
        $("rule_customer_individ_ids").removeClassName("required-entry");
        
        setRowVisibility("rule_customer_individ_ids", false);
    }    
}

// Event.observe($("rule_is_individ"), "change", bindIndivid);

// bindIndivid();

</script>

';
        return $sAppendHtml;
        
    }
} } 