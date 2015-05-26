<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Pmethod extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        $pay_method = $row->getData($this->getColumn()->getIndex());

        $pay_method = $pay_method == 'cashondelivery' ? 'Postpaid' : 'Prepaid';

        return $pay_method;
    }

}