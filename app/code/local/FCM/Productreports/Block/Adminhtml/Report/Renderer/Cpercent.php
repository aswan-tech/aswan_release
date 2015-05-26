<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Cpercent extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

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
		
        $query2 = 'SELECT discount_percent,discount_amount,original_price FROM ' . $resource->getTableName('sales_flat_order_item') . ' where item_id = "' . $result['parent_item_id'] . '" ';
        $result2 = $readConnection->fetchRow($query2);
        if($result2['discount_percent'] <= 0) {
			$result2['discount_percent'] = (($result2['discount_amount']*100) / $result2['original_price']);
		}
        
        return round($result2['discount_percent'], 2)."%";
    }

}