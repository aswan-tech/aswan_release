<?php

class FCM_Newsletters_Block_Adminhtml_Newsletter_Queue_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $actions = array();
		
        $actions[] = array(
            'url'       =>  $this->getUrl('*/newsletter_queue/preview',array('id'=>$row->getId())),
            'caption'   =>  Mage::helper('newsletter')->__('Preview'),
            'popup'     =>  true
        );
		
        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
