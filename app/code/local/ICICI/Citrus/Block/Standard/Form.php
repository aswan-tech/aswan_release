<?php
class ICICI_citrus_Block_Standard_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('citrus/standard/form.phtml');
        parent::_construct();
    }
}