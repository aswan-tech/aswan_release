<?php
/**
 * Magento Controller for providing the ability to set the order DC Status
 *
 * This defines functions to allow the user to set the DC status
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author	Pawan Prakash Gupta
 * @author_id	51405591
 * @company	HCL Technologies
 * @created Tuesday, October 23, 2012
 * @copyright	Four cross media
 */

/**
 * Controller overriding the Admin Sales Order Controller
 *
 * @category    FCM
 * @package     FCM_Fulfillment
 * @author      Pawan Prakash Gupta <51405591>
 */
 
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

class FCM_Fulfillment_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{
    
	/*
	 * Function to set the order status as 'Sent to DC'
	 * 
	 */
	 
	public function sentToDcAction() 
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUpdated = 0;
        $countNonUpdated = 0;

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
			$sentToErp = $order->getSentToErp();
			
            if ($sentToErp != 1) {
                $order->setSentToErp(1)
                    ->save();
                $countUpdated++;
            } else {
                $countNonUpdated++;
            }
        }
        if ($countNonUpdated) {
            if ($countUpdated) {
                $this->_getSession()->addError($this->__('%s order(s) DC Status was not changed.', $countNonUpdated));
            } else {
                $this->_getSession()->addError($this->__('No order(s) DC Status was changed.'));
            }
        }
        if ($countUpdated) {
            $this->_getSession()->addSuccess($this->__('%s order(s) DC Status was changed to `Sent To DC`', $countUpdated));
        }
		
       $this->_redirect('*/*/');
	}
	
	/*
	 * Function to set the order status as 'Not Sent to DC'
	 * 
	 */
	 
	public function notSentToDcAction() 
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $countUpdated = 0;
        $countNonUpdated = 0;

        foreach ($orderIds as $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
			$sentToErp = $order->getSentToErp();
			
            if ($sentToErp == 1) {
                $order->setSentToErp(0)
                    ->save();
                $countUpdated++;
            } else {
                $countNonUpdated++;
            }
        }
        if ($countNonUpdated) {
            if ($countUpdated) {
                $this->_getSession()->addError($this->__('%s order(s) DC Status was not changed.', $countNonUpdated));
            } else {
                $this->_getSession()->addError($this->__('No order(s) DC Status was changed.'));
            }
        }
        if ($countUpdated) {
            $this->_getSession()->addSuccess($this->__('%s order(s) DC Status was changed to `Not Sent To DC`', $countUpdated));
        }
		
       $this->_redirect('*/*/');
	}
}
