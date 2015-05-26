<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Dcstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $latest_status = $row->getData($this->getColumn()->getIndex());

        $statuses = $this->getDcStatuses();
        return $statuses[$latest_status];
    }

    public function getDcStatuses() {
        
        $statuses = array(
            '0' => 'Not Sent to DC',
            '1' => 'Sent to DC',
            '2' => 'Confirmed',
            '3' => 'Rejected',
            '4' => 'Shipped',
            '5' => 'Delivered',
            '6' => 'Not Delivered',
            '7'=>'Partial Shipped'
        );

        return $statuses;
    }

}