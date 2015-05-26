<?php

class FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Grid_Renderer_Mode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $value = $value == 'credit_card' ? 'Credit/Debit Card' : 'Net Banking';
        return $value;
    }

}

?>
