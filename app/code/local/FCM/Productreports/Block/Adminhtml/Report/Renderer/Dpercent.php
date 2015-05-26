<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Dpercent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

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

        $query2 = 'SELECT product_mrp, original_price FROM ' . $resource->getTableName('sales_flat_order_item') . ' where item_id = "' . $result['parent_item_id'] . '" ';
        $result2 = $readConnection->fetchRow($query2);

        $product_mrp = $result2['product_mrp'];
        $original_price = $result2['original_price'];

        if ($product_mrp) {
            $discount_percent = ( ( ( $product_mrp - $original_price ) / $product_mrp ) * 100 );
        } else {
            $discount_percent = '0.00';
        }

        return round($discount_percent, 2)."%";
    }

}