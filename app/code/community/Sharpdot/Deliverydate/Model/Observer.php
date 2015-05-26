<?php
/**
 * CustomSearch Observer
 *
 * @category    Exxex
 * @package     Essex_Customermod
 * @author      Sharpdotinc.com
 */
class Sharpdot_Deliverydate_Model_Observer
{				
	
	public function checkout_controller_onepage_save_shipping_method($observer)
	{
		$request = $observer->getEvent()->getRequest();
		$desiredArrivalDate = Mage::helper('deliverydate')->getFormatedDeliveryDateToSave($request->getPost('shipping_arrival_date', ''));
		$timeslot = $request->getPost('shipping_time_slot', '');
		Mage::getSingleton('core/session')->setArrivalDate($desiredArrivalDate);
		Mage::getSingleton('core/session')->setTimeSlot($timeslot);
	}
	
	public function checkout_type_onepage_save_order_after($observer)
	{
		$quote =  $observer->getEvent()->getOrder();
		$desiredArrivalDate = Mage::getSingleton('core/session')->getArrivalDate();
		$timeslot = Mage::getSingleton('core/session')->getTimeSlot();
		$quote->setShippingArrivalDate($desiredArrivalDate);
		$quote->setShippingTimeSlot($timeslot);
		$quote->save();
		return $this;
	}
		
}