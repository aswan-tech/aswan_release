<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Rtotal extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $item_id = $row->getData($this->getColumn()->getIndex());
        $_coreHelper = $this->helper('core');

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        
        $query = 'SELECT parent_item_id FROM ' . $resource->getTableName('sales_flat_order_item') . ' where item_id = "' . $item_id . '" ';
        $result = $readConnection->fetchRow($query);

        $query2 = 'SELECT row_total, tax_amount, hidden_tax_amount, weee_tax_applied_row_amount, discount_amount FROM ' . $resource->getTableName('sales_flat_order_item') . ' where item_id = "' . $result['parent_item_id'] . '" ';
        $result2 = $readConnection->fetchRow($query2);

        $row_total = $this->getItemTotal($result2);
        return number_format($row_total, 2);
        //return $_coreHelper->currency($row_total, true, false);
    }

    public function getItemTotal($item) {
        //return $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount();
        return $item['row_total'] + $item['tax_amount'] + $item['hidden_tax_amount'] + $item['weee_tax_applied_row_amount'] - $item['discount_amount'];
    }

}