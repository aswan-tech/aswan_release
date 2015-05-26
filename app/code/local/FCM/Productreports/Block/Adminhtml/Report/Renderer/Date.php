<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $created_at = $row->getData($this->getColumn()->getIndex());

        $timestamp = strtotime($created_at);
                $DateFormat = 'Y/m/d';

                $date = Mage::getModel('core/date')->date($DateFormat, $timestamp);
        //$date = date("Y-m-d", $timestamp);
        return $date;
    }
}