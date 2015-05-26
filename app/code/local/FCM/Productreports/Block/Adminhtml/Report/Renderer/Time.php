<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Time extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $created_at = $row->getData($this->getColumn()->getIndex());

        $timestamp = strtotime($created_at);
                $TimeFormat = 'H:i:s';
                $time = Mage::getModel('core/date')->date($TimeFormat, $timestamp);
        //$time = date("H:i:s", $timestamp);

        return $time;
    }
}