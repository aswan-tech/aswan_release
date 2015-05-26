<?php

class FCM_Logger_Block_Adminhtml_Grid_Renderer_Content extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
		return $row->getData($this->getColumn()->getIndex());
    }
} 