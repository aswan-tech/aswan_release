<?php

class FCM_Productreports_Block_Adminhtml_Report_Renderer_Giftcard extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render for Content
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
		$incrementId =  $row->getData('order_increment_id');
		$orders = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
		$cards = unserialize($orders->getGiftCards());
		return $cards[0]['c'];
    }

}
