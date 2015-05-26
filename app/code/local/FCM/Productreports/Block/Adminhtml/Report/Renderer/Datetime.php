<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $created_at = $row->getData($this->getColumn()->getIndex());
                $DateFormat = 'Y/m/d';
                $TimeFormat = 'H:i:s';

        $timestamp = strtotime($created_at);
                $date = Mage::getModel('core/date')->date($DateFormat, $timestamp) .' '.Mage::getModel('core/date')->date($TimeFormat, $timestamp);
                return $date;
    }

}