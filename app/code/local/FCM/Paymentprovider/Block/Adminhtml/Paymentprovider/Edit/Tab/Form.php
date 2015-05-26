<?php

class FCM_Paymentprovider_Block_Adminhtml_Paymentprovider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("paymentprovider_form", array("legend" => Mage::helper("paymentprovider")->__("Payment Provider Information")));
        $methods = $this->getActivPaymentMethods();
        
        $fieldset->addField("payment_method_type", "select", array(
            "label" => Mage::helper("paymentprovider")->__("Payment Mode"),
            "name" => "payment_method_type",
            "class" => "required-entry",
            "required" => true,
            "values" => array(
                array(
                    "value" => 'credit_card',
                    "label" => Mage::helper("paymentprovider")->__("Credit/Debit Card"),
                ),
                array(
                    "value" => 'net_banking',
                    "label" => Mage::helper("paymentprovider")->__("Net Banking"),
                ),
            ),
        ));

        $fieldset->addField("payment_method_name", "text", array(
            "label" => Mage::helper("paymentprovider")->__("Payment Method"),
            "class" => "required-entry",
            "required" => true,
            "name" => "payment_method_name",
        ));

        $fieldset->addField("payment_method_code", "select", array(
            "label" => Mage::helper("paymentprovider")->__("Payment Provider"),
            "name" => "payment_method_code",
            "class" => "required-entry",
            "required" => true,
            "values" => $methods
        ));

        if (Mage::getSingleton("adminhtml/session")->getPaymentData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getPaymentData());
            Mage::getSingleton("adminhtml/session")->setPaymentData(null);
        } elseif (Mage::registry("paymentprovider_data")) {
            $form->setValues(Mage::registry("paymentprovider_data")->getData());
        }
        return parent::_prepareForm();
    }

    public function getActivPaymentMethods() {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            if ($paymentCode != 'cashondelivery' && $paymentCode != 'free') {
                $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
                $methods[$paymentCode] = array(
                    'label' => $paymentTitle,
                    'value' => $paymentCode,
                );
            }
        }
        return $methods;
    }

}