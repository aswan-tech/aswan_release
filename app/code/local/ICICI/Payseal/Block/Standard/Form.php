<?php
class ICICI_payseal_Block_Standard_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('payseal/standard/form.phtml');
        parent::_construct();
    }
}