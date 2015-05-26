<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Mrp extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

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
        $query = 'SELECT product_mrp FROM ' . $resource->getTableName('sales_flat_order_item') . ' where item_id = "' . $item_id . '" ';
        $result = $readConnection->fetchRow($query);

        if ($result['product_mrp']) {
            return number_format($result['product_mrp'], 2);
            //return $_coreHelper->currency($result['product_mrp'], true, false);
        } else {
			return 0;
            //return $_coreHelper->currency('0', true, false);
        }
    }

}