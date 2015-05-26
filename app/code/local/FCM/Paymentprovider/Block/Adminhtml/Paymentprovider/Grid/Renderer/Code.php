<?php

class FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Grid_Renderer_Code extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        $value = $this->getPaymentMethodTitle($value);
        return $value;
    }

    public function getPaymentMethodTitle($paymentCodeValue) {

        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $methods[$paymentCode] = $paymentCode;
        }

        if(in_array($paymentCodeValue, $methods)){
            $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCodeValue . '/title');
        }else{
            $paymentTitle = 'Disabled From System Config';
        }
       
        return $paymentTitle;
    }

}

?>
