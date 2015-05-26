<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Custid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $customer_email = $row->getData($this->getColumn()->getIndex());

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query = 'SELECT entity_id FROM ' . $resource->getTableName('customer_entity') . ' where email = "'.$customer_email.'" ';
        $result = $readConnection->fetchRow($query);

        return $result['entity_id'];

    }

}