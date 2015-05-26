<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Lstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $latest_status = $row->getData($this->getColumn()->getIndex());

        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        return $statuses[$latest_status];
    }

}