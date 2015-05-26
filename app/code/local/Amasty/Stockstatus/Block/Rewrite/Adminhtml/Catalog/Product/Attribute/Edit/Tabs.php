<?php
/**
* @copyright Amasty.
*/
class Amasty_Stockstatus_Block_Rewrite_Adminhtml_Catalog_Product_Attribute_Edit_Tabs 
                        extends Amasty_Stockstatus_Block_Rewrite_Adminhtml_Catalog_Product_Attribute_Edit_Tabs_Pure
{
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        if ('custom_stock_status' == Mage::registry('entity_attribute')->getData('attribute_code'))
        {
            $this->addTab('icons', array(
                'label'     => Mage::helper('amstockstatus')->__('Manage Icons'),
                'title'     => Mage::helper('amstockstatus')->__('Manage Icons'),
                'content'   => $this->getLayout()->createBlock('amstockstatus/icons')->toHtml(),
            ));
            $this->addTab('ranges', array(
                'label'     => Mage::helper('amstockstatus')->__('Quantity Range Statuses'),
                'title'     => Mage::helper('amstockstatus')->__('Quantity Range Statuses'),
                'content'   => $this->getLayout()->createBlock('amstockstatus/ranges')->toHtml(),
            ));
            return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
        }
        
        return $this;
    }
}