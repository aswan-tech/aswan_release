<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Tpercent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $item_id = $row->getData($this->getColumn()->getIndex());

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = 'SELECT parent_item_id FROM ' . $resource->getTableName('sales_flat_order_item') . ' where item_id = "' . $item_id . '" ';
        $result = $readConnection->fetchRow($query);

        $query2 = 'SELECT tax_percent FROM ' . $resource->getTableName('sales_flat_order_item') . ' where item_id = "' . $result['parent_item_id'] . '" ';
        $result2 = $readConnection->fetchRow($query2);
        
        return round($result2['tax_percent'], 2)."%";

    }

}