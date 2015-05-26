<?php

class FCM_Logger_Varien_Data_Form_Element_Content extends Varien_Data_Form_Element_Abstract 
{
    public function __construct($data) 
	{
        parent::__construct($data);
        $this->setType('content');
    }

    public function getElementHtml() 
	{
        return $this->getValue();
    }
}