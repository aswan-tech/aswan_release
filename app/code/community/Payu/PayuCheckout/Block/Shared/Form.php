<?php

class Payu_PayuCheckout_Block_Shared_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('payucheckout/shared/form.phtml');
        parent::_construct();
    }
}