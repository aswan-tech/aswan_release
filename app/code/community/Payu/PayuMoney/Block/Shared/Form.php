<?php

class Payu_PayuMoney_Block_Shared_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('payumoney/shared/form.phtml');
        parent::_construct();
    }
}