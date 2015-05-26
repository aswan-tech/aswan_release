<?php
/**
 * Product:     Loyalty Program for Enterprise Edition
 * Package:     Aitoc_Aitloyalty_10.0.10_574534
 * Purchase ID: INzRIwyyaNoeOLERhAgt4U28qVKIeEa3dfPrgaAN3C
 * Generated:   2013-05-13 06:36:55
 * File path:   app/code/local/Aitoc/Aitloyalty/Block/Rewrite/AdminhtmlPromoQuoteEditTabs.php
 * Copyright:   (c) 2013 AITOC, Inc.
 */
?>
<?php if(Aitoc_Aitsys_Abstract_Service::initSource(__FILE__,'Aitoc_Aitloyalty')){ DYBMggDUPmDaTyIP('07414f792495b4a4b9dbf65e79504e4a'); ?><?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */

class Aitoc_Aitloyalty_Block_Rewrite_AdminhtmlPromoQuoteEditTabs extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs
{
	
    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('salesrule')->__('Rule Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/promo_quote_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('salesrule')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('adminhtml/promo_quote_edit_tab_conditions')->toHtml(),
        ));

        $this->addTab('actions_section', array(
            'label'     => Mage::helper('salesrule')->__('Actions'),
            'content'   => $this->getLayout()->createBlock('adminhtml/promo_quote_edit_tab_actions')->toHtml(),
        ));
        
        $this->addTab('aitoc_display_section', array(
            'label'     => Mage::helper('salesrule')->__('Display Options'),
            'content'   => $this->getLayout()->createBlock('aitloyalty/quote_options')->toHtml(),
        ));

        return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
    }

} } 